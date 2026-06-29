<?php
declare(strict_types=1);

if (!function_exists('getPartnerCommissionRate')) {
    function getPartnerCommissionRate($pdo, $partnerId, $campaignId = null): float
    {
        $partnerId = (int)$partnerId;
        if ($partnerId <= 0) {
            return 0.0;
        }

        // Fetch partner details
        $stmt = $pdo->prepare("SELECT partner_type FROM lms_affiliate_partners WHERE id = ? LIMIT 1");
        $stmt->execute([$partnerId]);
        $partner = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$partner) {
            return 0.0;
        }

        $partnerType = $partner['partner_type'];

        if ($partnerType === 'individual') {
            // Individual partner track: based on referrals count
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM lms_affiliate_referrals WHERE partner_id = ? AND status = 'enrolled'");
            $stmtCount->execute([$partnerId]);
            $count = (int)$stmtCount->fetchColumn();

            if ($count <= 1) {
                return 10.0;
            } elseif ($count === 2) {
                return 15.0;
            } elseif ($count >= 3 && $count <= 5) {
                return 20.0;
            } else {
                return 25.0;
            }
        } else {
            // Organization / Institution / Private / Government track
            // Commissions are based on the campaign's discount_rate
            if ($campaignId) {
                $stmtCamp = $pdo->prepare("SELECT discount_rate FROM lms_affiliate_campaigns WHERE id = ? AND partner_id = ? LIMIT 1");
                $stmtCamp->execute([(int)$campaignId, $partnerId]);
                $rate = $stmtCamp->fetchColumn();
                if ($rate !== false) {
                    return (float)$rate;
                }
            }

            // Fallback or default commission rate for non-individual tracks
            return 15.0; // default/fallback
        }
    }
}

if (!function_exists('processCampaignPayment')) {
    function processCampaignPayment($pdo, $studentId, $enrollmentId, $due, $partnerId, $campaignId = null): string
    {
        $rate = getPartnerCommissionRate($pdo, $partnerId, $campaignId);
        $commission = round($due * ($rate / 100), 2);
        $netAmount = round($due - $commission, 2);

        // Fetch current paid amount
        $stmtEnroll = $pdo->prepare("SELECT paid_amount, course_id FROM lms_enrollments WHERE id = ? AND student_id = ? LIMIT 1");
        $stmtEnroll->execute([(int)$enrollmentId, (int)$studentId]);
        $enroll = $stmtEnroll->fetch(PDO::FETCH_ASSOC);
        if (!$enroll) {
            throw new RuntimeException('Enrollment record not found.');
        }

        // Fetch course price
        $stmtCourse = $pdo->prepare("SELECT price FROM lms_courses WHERE id = ? LIMIT 1");
        $stmtCourse->execute([(int)$enroll['course_id']]);
        $coursePrice = (float)$stmtCourse->fetchColumn();

        // Check if student JSS/SSS
        $stmtStudent = $pdo->prepare("SELECT is_affiliate, affiliate_class_range FROM lms_students WHERE id = ? LIMIT 1");
        $stmtStudent->execute([(int)$studentId]);
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);
        
        $isAffiliate = !empty($student['is_affiliate']);
        $classRange = $student['affiliate_class_range'] ?? '';
        if ($isAffiliate && ($classRange === 'JSS' || $classRange === 'SSS')) {
            $coursePrice = min($coursePrice, 5000.0);
        }

        $newPaidAmount = $coursePrice; // Course is fully unlocked

        // Perform updates inside transactional boundary
        $inTransaction = $pdo->inTransaction();
        if (!$inTransaction) {
            $pdo->beginTransaction();
        }

        try {
            // Update enrollment status to paid
            $updEnroll = $pdo->prepare("UPDATE lms_enrollments SET paid_amount = ?, status = 'paid' WHERE id = ? AND student_id = ?");
            $updEnroll->execute([$newPaidAmount, (int)$enrollmentId, (int)$studentId]);

            // Insert success campaign payment record
            $campaignRef = 'CAMP_' . bin2hex(random_bytes(6));
            $payStmt = $pdo->prepare("
                INSERT INTO lms_payments (student_id, enrollment_id, amount, channel, reference, status, created_at)
                VALUES (?, ?, ?, 'campaign', ?, 'success', NOW())
            ");
            $payStmt->execute([(int)$studentId, (int)$enrollmentId, $netAmount, $campaignRef]);

            if (!$inTransaction) {
                $pdo->commit();
            }

            return $campaignRef;
        } catch (Throwable $e) {
            if (!$inTransaction) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
