<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/guard.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/live_session_tools.php';
require_once __DIR__ . '/config/db.php';

$sessionId = (int)($_GET['id'] ?? 0);
if ($sessionId <= 0) {
    http_response_code(400);
    exit('Invalid session.');
}

$userId = (int)($_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? $_SESSION['instructor']['id'] ?? 0);
$studentId = (int)($_SESSION['user']['id'] ?? 0);
$isAdmin = !empty($_SESSION['admin']) || (($_SESSION['user']['role'] ?? '') === 'admin');
$isInstructor = !empty($_SESSION['instructor']) || (($_SESSION['user']['role'] ?? '') === 'instructor');
$displayName = trim((string)(
    ($_SESSION['user']['first_name'] ?? $_SESSION['instructor']['full_name'] ?? $_SESSION['admin']['full_name'] ?? '')
    . ' '
    . ($_SESSION['user']['last_name'] ?? '')
));
if ($displayName === '') {
    $displayName = $isInstructor ? 'Instructor' : ($isAdmin ? 'Admin' : 'Student');
}

if ($userId <= 0) {
    redirect('login.php');
}

if ($isInstructor) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.title AS course_title, i.full_name AS instructor_name
        FROM lms_live_sessions s
        JOIN lms_courses c ON c.id = s.course_id
        LEFT JOIN lms_instructors i ON i.id = s.instructor_id
        WHERE s.id = ? AND s.instructor_id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId, $userId]);
} elseif ($isAdmin) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.title AS course_title, i.full_name AS instructor_name
        FROM lms_live_sessions s
        JOIN lms_courses c ON c.id = s.course_id
        LEFT JOIN lms_instructors i ON i.id = s.instructor_id
        WHERE s.id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId]);
} else {
    $stmt = $pdo->prepare("
        SELECT s.*, c.title AS course_title, i.full_name AS instructor_name
        FROM lms_live_sessions s
        JOIN lms_courses c ON c.id = s.course_id
        LEFT JOIN lms_instructors i ON i.id = s.instructor_id
        JOIN lms_enrollments e ON e.course_id = s.course_id
        WHERE s.id = ? AND e.student_id = ?
        LIMIT 1
    ");
    $stmt->execute([$sessionId, $studentId]);
}

$session = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$session) {
    http_response_code(403);
    exit('You do not have access to this session.');
}

if (!$isInstructor && !$isAdmin) {
    $joinedStmt = $pdo->prepare("SELECT id FROM lms_session_attendance WHERE session_id = ? AND student_id = ? LIMIT 1");
    $joinedStmt->execute([$sessionId, $studentId]);
    $alreadyJoined = (bool)$joinedStmt->fetchColumn();

    if (!$alreadyJoined) {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $weekStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM lms_session_attendance a
            JOIN lms_live_sessions s ON s.id = a.session_id
            WHERE a.student_id = ?
              AND DATE(s.scheduled_at) BETWEEN ? AND ?
        ");
        $weekStmt->execute([$studentId, $weekStart, $weekEnd]);
        $weekCount = (int)$weekStmt->fetchColumn();

        if ($weekCount >= 2) {
            $_SESSION['live_error'] = 'You have reached the 2 live sessions per week limit.';
            redirect('live_session.php');
        }

        $pdo->prepare("INSERT IGNORE INTO lms_session_attendance (session_id, student_id) VALUES (?, ?)")
            ->execute([$sessionId, $studentId]);
    }
}

$chatEnabled = liveSessionChatTableExists($pdo);
$participantKey = bin2hex(random_bytes(12));
$role = $isAdmin ? 'admin' : ($isInstructor ? 'instructor' : 'student');
$recordingUrl = trim((string)($session['recording_url'] ?? ''));
$meetingLink = trim((string)($session['meeting_link'] ?? ''));
$meetingProvider = liveSessionProvider($meetingLink);
$meetingProviderLabel = liveSessionProviderLabel($meetingLink);
$isTeamsRoom = liveSessionIsTeams($meetingLink);
$iceServers = liveSessionIceServers();
$hasTurnServer = liveSessionHasTurnServer();
$hasIceServers = liveSessionHasIceServers();
$forceRelay = liveSessionForceRelay();
$attendanceCount = 0;
try {
    $attendanceCount = (int)$pdo->query("SELECT COUNT(*) FROM lms_session_attendance WHERE session_id = {$sessionId}")->fetchColumn();
} catch (Throwable $e) {
}
?>
<!doctype html>
<html lang="en">
<head>
<?php
$seoTitle   = 'Live Classroom';
$seoDesc    = $isTeamsRoom
    ? 'Microsoft Teams live session room inside the LMS with attendance and chat.'
    : 'Native LMS live classroom with video, chat, attendance, and recording.';
$seoNoIndex = true;
require_once __DIR__ . '/includes/seo.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet">
<style>
  .class-grid { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(330px,.75fr); gap:1rem; }
  .panel { background:#fff; border:1px solid var(--border); border-radius:18px; overflow:hidden; }
  .panel-head { padding:1rem 1.25rem; border-bottom:1px solid var(--border); }
  .panel-body { padding:1rem 1.25rem; }
  .stage-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
  .video-card { border:1px solid var(--border); border-radius:16px; overflow:hidden; background:#0f172a; position:relative; min-height:230px; }
  .video-card video { width:100%; height:100%; object-fit:cover; background:#020617; }
  .video-label { position:absolute; left:.75rem; bottom:.75rem; background:rgba(15,23,42,.75); color:#fff; border-radius:999px; padding:.3rem .65rem; font-size:.78rem; }
  .toolbar { display:flex; gap:.65rem; flex-wrap:wrap; }
  .toolbar button { border:1px solid var(--border); background:#fff; border-radius:999px; padding:.55rem .9rem; font-weight:600; }
  .toolbar button.active { background:var(--brand); border-color:var(--brand); color:#fff; }
  .chat-wrap { display:flex; flex-direction:column; min-height:620px; }
  .chat-log { flex:1; overflow:auto; display:flex; flex-direction:column; gap:.75rem; padding-bottom:1rem; }
  .chat-item { border:1px solid var(--border); border-radius:14px; padding:.75rem .9rem; background:#f8fafc; }
  .chat-item.self { background:#eef6ff; border-color:#bfdbfe; }
  .chat-meta { font-size:.74rem; color:var(--muted); margin-bottom:.25rem; }
  .participant-list { display:flex; flex-direction:column; gap:.55rem; max-height:190px; overflow:auto; }
  .participant-row { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.55rem .7rem; border:1px solid var(--border); border-radius:12px; font-size:.85rem; }
  .participant-pill { font-size:.72rem; border-radius:999px; padding:.18rem .5rem; background:#eef2ff; color:#3730a3; }
  .screen-note { font-size:.82rem; color:var(--muted); }
  .teams-stage { min-height:520px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#f8fafc,#eef2ff); border-radius:16px; }
  .teams-room-card { max-width:680px; padding:2rem; text-align:center; }
  .teams-room-icon { width:72px; height:72px; border-radius:18px; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; background:#6264a7; color:#fff; font-size:2rem; }
  .teams-room-actions { display:flex; justify-content:center; gap:.75rem; flex-wrap:wrap; }
  .teams-room-note { color:var(--muted); font-size:.88rem; margin-top:1rem; }
  @media (max-width: 991px) {
    .class-grid { grid-template-columns:1fr; }
    .stage-grid { grid-template-columns:1fr; }
  }
</style>
</head>
<body style="background:var(--surface)">
<?php if ($isAdmin || $isInstructor): ?>
<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-white text-decoration-none" href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'instructor_dashboard.php'; ?>">Grafix@Mirror</a>
    <div class="ms-auto d-flex gap-2">
      <?php if ($isAdmin): ?>
        <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        <a href="admin_logout.php" class="btn btn-danger btn-sm">Logout</a>
      <?php else: ?>
        <a href="instructor_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        <a href="instructor_logout.php" class="btn btn-danger btn-sm">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<?php else: ?>
<nav class="lms-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="brand text-decoration-none">
      <div style="width:32px;height:32px;background:var(--brand);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem">G</div>
      <span>Grafix<span style="color:var(--brand)">@Mirror</span></span>
    </a>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn-ghost"><i class="fa fa-th-large me-1"></i>Dashboard</a>
      <a href="logout.php" class="btn-ghost" style="color:var(--danger)">Logout</a>
    </div>
  </div>
</nav>
<?php endif; ?>

<div class="container py-4" style="max-width:1280px">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h4 class="page-title mb-1"><i class="fa fa-tower-broadcast me-2"></i><?= e((string)$session['title']) ?></h4>
      <?php if ($isTeamsRoom): ?>
        <div class="mb-1" style="font-size:.8rem;color:#6264a7;font-weight:700">
          <i class="fa-brands fa-microsoft me-1"></i>Microsoft Teams channel room
        </div>
      <?php endif; ?>
      <div class="text-muted" style="font-size:.88rem">
        <?= e((string)$session['course_title']) ?> · Native LMS classroom · <?= (int)$attendanceCount ?> attendee<?= $attendanceCount !== 1 ? 's' : '' ?>
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <?php if ($recordingUrl !== ''): ?>
        <a href="<?= e($recordingUrl) ?>" target="_blank" class="btn-brand"><i class="fa fa-play-circle me-1"></i>Watch Recording</a>
      <?php endif; ?>
      <span class="badge-<?= ($session['status'] ?? '') === 'live' ? 'danger' : 'info' ?>"><?= e(ucfirst((string)$session['status'])) ?></span>
    </div>
  </div>

  <div class="class-grid">
    <section class="panel">
      <div class="panel-head">
        <div style="font-weight:700"><?= e($isTeamsRoom ? 'Microsoft Teams Channel' : 'LMS Classroom Stage') ?></div>
        <div class="text-muted" style="font-size:.82rem">
          <?= e($isTeamsRoom
            ? 'Attendance, participant presence, and LMS chat stay here while the live video runs through Microsoft Teams.'
            : 'Browser video/audio, screen share for demos, in-LMS attendance, and chat. No Zoom, Meet, or AnyDesk required.') ?>
        </div>
      </div>
      <div class="panel-body">
        <?php if ($isTeamsRoom): ?>
          <div class="teams-stage">
            <div class="teams-room-card">
              <div class="teams-room-icon"><i class="fa-brands fa-microsoft"></i></div>
              <h5 class="mb-2">Join the Teams live room</h5>
              <p class="text-muted mb-4">
                This session is connected to a Microsoft Teams meeting. Keep this LMS room open for attendance and class chat.
              </p>
              <div class="teams-room-actions">
                <a href="<?= e($meetingLink) ?>" target="_blank" rel="noopener" class="btn-brand">
                  <i class="fa fa-arrow-up-right-from-square me-1"></i>Open Teams Room
                </a>
                <button type="button" class="btn-ghost" id="copyTeamsLinkBtn" data-link="<?= e($meetingLink) ?>">
                  <i class="fa fa-link me-1"></i>Copy Link
                </button>
              </div>
              <div class="teams-room-note">
                Microsoft Teams may open in the browser or the Teams app depending on device and organization settings.
              </div>
            </div>
          </div>
        <?php else: ?>
        <div class="toolbar mb-3">
          <button type="button" id="toggleMicBtn" class="active"><i class="fa fa-microphone me-1"></i>Mic</button>
          <button type="button" id="toggleCamBtn" class="active"><i class="fa fa-video me-1"></i>Camera</button>
          <button type="button" id="shareScreenBtn"><i class="fa fa-display me-1"></i>Share Screen</button>
          <?php if ($isInstructor || $isAdmin): ?>
            <button type="button" id="recordBtn"><i class="fa fa-record-vinyl me-1"></i>Start Recording</button>
          <?php endif; ?>
        </div>
        <div class="screen-note mb-3">For lab demos or one-on-one guidance, instructors can use <strong>Share Screen</strong> and students can interact through chat while staying inside the LMS.</div>
        <div class="stage-grid" id="videoStage">
          <div class="video-card">
            <video id="localVideo" autoplay muted playsinline></video>
            <div class="video-label">You</div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <aside class="panel">
      <div class="panel-head">
        <div style="font-weight:700">Classroom Sidebar</div>
      </div>
      <div class="panel-body chat-wrap">
        <div class="mb-3">
          <div class="small text-muted">Instructor</div>
          <div class="fw-semibold"><?= e((string)($session['instructor_name'] ?? 'Instructor')) ?></div>
        </div>
        <div class="mb-3">
          <div class="small text-muted">Participants</div>
          <div class="participant-list" id="participantList">
            <div class="participant-row"><span>Loading participants...</span></div>
          </div>
        </div>
        <div class="mb-3">
        <div class="small text-muted">Session Chat</div>
        </div>
        <div class="mb-3">
          <div class="small text-muted">Connectivity</div>
          <div class="fw-semibold">
            <?= $isTeamsRoom ? e($meetingProviderLabel) : ($hasTurnServer ? 'TURN relay configured' : ($hasIceServers ? 'STUN only / limited NAT traversal' : 'No ICE server configured')) ?>
          </div>
          <div class="text-muted" style="font-size:.8rem">
            <?= $isTeamsRoom
              ? 'Video and audio are handled by Microsoft Teams; this LMS room keeps attendance, participant presence, and class chat active.'
              : ($hasTurnServer
              ? 'This classroom can relay traffic through your own TURN server when direct peer connections fail.'
              : ($hasIceServers
                ? 'Add self-hosted TURN credentials in `.env` for stronger cross-network connectivity.'
                : 'Add self-hosted STUN/TURN settings in `.env` before using this classroom outside a simple local network.')) ?>
          </div>
        </div>
        <div class="chat-log" id="chatLog">
          <div class="text-muted" style="font-size:.85rem">Loading chat…</div>
        </div>
        <form id="chatForm" class="mt-2">
          <textarea id="chatInput" class="form-control mb-2" rows="3" placeholder="Ask a question, respond to the lecture, or request lab help…"></textarea>
          <button class="btn-brand w-100"><i class="fa fa-paper-plane me-1"></i>Send</button>
        </form>
      </div>
    </aside>
  </div>
</div>

<script>
const classroomConfig = {
  sessionId: <?= (int)$sessionId ?>,
  userId: <?= (int)$userId ?>,
  role: <?= json_encode($role) ?>,
  displayName: <?= json_encode($displayName) ?>,
  participantKey: <?= json_encode($participantKey) ?>,
  csrf: <?= json_encode(csrfToken()) ?>,
  recordingEnabled: <?= ($isInstructor || $isAdmin) ? 'true' : 'false' ?>,
  isTeamsRoom: <?= $isTeamsRoom ? 'true' : 'false' ?>,
  meetingProvider: <?= json_encode($meetingProvider) ?>,
  meetingProviderLabel: <?= json_encode($meetingProviderLabel) ?>,
  iceServers: <?= json_encode($iceServers, JSON_UNESCAPED_SLASHES) ?>,
  forceRelay: <?= $forceRelay ? 'true' : 'false' ?>
};

const localVideo = document.getElementById('localVideo');
const videoStage = document.getElementById('videoStage');
const participantList = document.getElementById('participantList');
const chatLog = document.getElementById('chatLog');
const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');
const toggleMicBtn = document.getElementById('toggleMicBtn');
const toggleCamBtn = document.getElementById('toggleCamBtn');
const shareScreenBtn = document.getElementById('shareScreenBtn');
const recordBtn = document.getElementById('recordBtn');
const copyTeamsLinkBtn = document.getElementById('copyTeamsLinkBtn');

let localStream = null;
let screenStream = null;
let recorder = null;
let recorderChunks = [];
let participants = [];
const peers = new Map();
const remoteCards = new Map();
const pendingIceCandidates = new Map();
let chatCursor = 0;

async function api(action, payload = {}) {
  const response = await fetch('live_session_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ action, session_id: classroomConfig.sessionId, participant_key: classroomConfig.participantKey, csrf: classroomConfig.csrf, ...payload })
  });
  return response.json();
}

function buildPeer(otherKey) {
  if (peers.has(otherKey)) {
    return peers.get(otherKey);
  }
  const pc = new RTCPeerConnection({
    iceServers: classroomConfig.iceServers,
    iceTransportPolicy: classroomConfig.forceRelay ? 'relay' : 'all'
  });
  if (localStream) {
    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));
  }
  pc.onicecandidate = (event) => {
    if (event.candidate) {
      api('send_signal', { to_key: otherKey, signal_type: 'ice', payload: JSON.stringify(event.candidate) });
    }
  };
  pc.ontrack = (event) => attachRemoteTrack(otherKey, event);
  peers.set(otherKey, pc);
  return pc;
}

function ensureRemoteCard(otherKey) {
  if (!remoteCards.has(otherKey)) {
    const wrapper = document.createElement('div');
    wrapper.className = 'video-card';
    wrapper.dataset.participant = otherKey;
    const video = document.createElement('video');
    video.autoplay = true;
    video.playsInline = true;
    video.setAttribute('playsinline', 'true');
    const audio = document.createElement('audio');
    audio.autoplay = true;
    const label = document.createElement('div');
    label.className = 'video-label';
    label.textContent = participantName(otherKey);
    wrapper.appendChild(video);
    wrapper.appendChild(audio);
    wrapper.appendChild(label);
    videoStage.appendChild(wrapper);
    remoteCards.set(otherKey, { wrapper, video, audio, label, stream: new MediaStream() });
  }
  return remoteCards.get(otherKey);
}

function attachRemoteTrack(otherKey, event) {
  const card = ensureRemoteCard(otherKey);
  const stream = event.streams && event.streams[0] ? event.streams[0] : card.stream;
  if (event.track && !stream.getTracks().some((track) => track.id === event.track.id)) {
    stream.addTrack(event.track);
  }
  card.stream = stream;
  card.video.srcObject = stream;
  card.audio.srcObject = stream;
  card.label.textContent = participantName(otherKey);
  card.video.play().catch(() => {});
  card.audio.play().catch(() => {});
}

async function flushPendingIce(otherKey, pc) {
  const queue = pendingIceCandidates.get(otherKey) || [];
  if (!queue.length || !pc.remoteDescription) {
    return;
  }
  pendingIceCandidates.delete(otherKey);
  for (const candidate of queue) {
    try {
      await pc.addIceCandidate(candidate);
    } catch (error) {
      console.warn('Queued ICE candidate skipped', error);
    }
  }
}

async function queueOrApplyIceCandidate(otherKey, pc, candidate) {
  if (!pc.remoteDescription) {
    const queue = pendingIceCandidates.get(otherKey) || [];
    queue.push(candidate);
    pendingIceCandidates.set(otherKey, queue);
    return;
  }
  await pc.addIceCandidate(candidate);
}

function removeRemoteStream(otherKey) {
  const card = remoteCards.get(otherKey);
  if (card) {
    card.wrapper.remove();
    remoteCards.delete(otherKey);
  }
  const pc = peers.get(otherKey);
  if (pc) {
    pc.close();
    peers.delete(otherKey);
  }
  pendingIceCandidates.delete(otherKey);
}

function participantName(otherKey) {
  const found = participants.find((p) => p.participant_key === otherKey);
  return found ? `${found.display_name}${found.role === 'instructor' ? ' (Instructor)' : found.role === 'admin' ? ' (Admin)' : ''}` : 'Participant';
}

async function ensureOffer(otherKey) {
  const pc = buildPeer(otherKey);
  const offer = await pc.createOffer();
  await pc.setLocalDescription(offer);
  await api('send_signal', { to_key: otherKey, signal_type: 'offer', payload: JSON.stringify(offer) });
}

async function handleSignals() {
  const data = await api('poll_signals');
  for (const signal of (data.signals || [])) {
    const otherKey = signal.from_key;
    const pc = buildPeer(otherKey);
    const payload = JSON.parse(signal.payload || '{}');
    if (signal.signal_type === 'offer') {
      await pc.setRemoteDescription(payload);
      const answer = await pc.createAnswer();
      await pc.setLocalDescription(answer);
      await flushPendingIce(otherKey, pc);
      await api('send_signal', { to_key: otherKey, signal_type: 'answer', payload: JSON.stringify(answer) });
    } else if (signal.signal_type === 'answer') {
      await pc.setRemoteDescription(payload);
      await flushPendingIce(otherKey, pc);
    } else if (signal.signal_type === 'ice') {
      try {
        await queueOrApplyIceCandidate(otherKey, pc, payload);
      } catch (error) {
        console.warn('ICE candidate skipped', error);
      }
    }
  }
}

async function refreshParticipants() {
  const data = await api('participants');
  participants = data.participants || [];
  participantList.innerHTML = '';
  const seen = new Set();

  participants.forEach((participant) => {
    const row = document.createElement('div');
    row.className = 'participant-row';
    row.innerHTML = `<span>${participant.display_name}</span><span class="participant-pill">${participant.role}</span>`;
    participantList.appendChild(row);

    if (classroomConfig.isTeamsRoom) {
      return;
    }

    if (participant.participant_key !== classroomConfig.participantKey) {
      seen.add(participant.participant_key);
      ensureRemoteCard(participant.participant_key).label.textContent = participantName(participant.participant_key);
      if (!peers.has(participant.participant_key) && classroomConfig.participantKey < participant.participant_key) {
        ensureOffer(participant.participant_key).catch((error) => console.warn('Offer error', error));
      }
    }
  });

  Array.from(peers.keys()).forEach((key) => {
    if (!seen.has(key)) {
      removeRemoteStream(key);
    }
  });
}

async function refreshChat() {
  const data = await api('chat_messages', { after_id: chatCursor });
  const messages = data.messages || [];
  if (messages.length && chatCursor === 0) {
    chatLog.innerHTML = '';
  }
  messages.forEach((msg) => {
    chatCursor = Math.max(chatCursor, Number(msg.id));
    const item = document.createElement('div');
    item.className = 'chat-item' + (msg.is_self ? ' self' : '');
    item.innerHTML = `<div class="chat-meta">${msg.sender_name} · ${msg.sender_role} · ${msg.time_label}</div><div>${msg.message_html}</div>`;
    chatLog.appendChild(item);
  });
  if (messages.length) {
    chatLog.scrollTop = chatLog.scrollHeight;
  }
}

async function startMedia() {
  localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
  localVideo.srcObject = localStream;
}

function replaceOutgoingTrack(kind, newTrack, stream) {
  peers.forEach((pc) => {
    const sender = pc.getSenders().find((s) => s.track && s.track.kind === kind);
    if (sender) {
      sender.replaceTrack(newTrack);
    } else {
      pc.addTrack(newTrack, stream);
    }
  });
}

if (toggleMicBtn) toggleMicBtn.addEventListener('click', () => {
  if (!localStream) return;
  const track = localStream.getAudioTracks()[0];
  if (!track) return;
  track.enabled = !track.enabled;
  toggleMicBtn.classList.toggle('active', track.enabled);
});

if (toggleCamBtn) toggleCamBtn.addEventListener('click', () => {
  if (!localStream) return;
  const track = localStream.getVideoTracks()[0];
  if (!track) return;
  track.enabled = !track.enabled;
  toggleCamBtn.classList.toggle('active', track.enabled);
});

if (shareScreenBtn) shareScreenBtn.addEventListener('click', async () => {
  try {
    if (!screenStream) {
      screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
      const screenTrack = screenStream.getVideoTracks()[0];
      replaceOutgoingTrack('video', screenTrack, screenStream);
      localVideo.srcObject = screenStream;
      shareScreenBtn.classList.add('active');
      screenTrack.onended = async () => {
        shareScreenBtn.classList.remove('active');
        screenStream = null;
        localVideo.srcObject = localStream;
        const camTrack = localStream.getVideoTracks()[0];
        if (camTrack) replaceOutgoingTrack('video', camTrack, localStream);
      };
    } else {
      screenStream.getTracks().forEach((track) => track.stop());
    }
  } catch (error) {
    console.warn('Screen share failed', error);
  }
});

if (copyTeamsLinkBtn) copyTeamsLinkBtn.addEventListener('click', async () => {
  const link = copyTeamsLinkBtn.dataset.link || '';
  if (!link) return;
  await navigator.clipboard.writeText(link);
  copyTeamsLinkBtn.innerHTML = '<i class="fa fa-check me-1"></i>Copied';
  setTimeout(() => {
    copyTeamsLinkBtn.innerHTML = '<i class="fa fa-link me-1"></i>Copy Link';
  }, 1800);
});

chatForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  const message = chatInput.value.trim();
  if (!message) return;
  await api('send_chat', { message });
  chatInput.value = '';
  refreshChat();
});

if (recordBtn) {
  recordBtn.addEventListener('click', async () => {
    if (!classroomConfig.recordingEnabled) return;
    if (!recorder) {
      const sourceStream = screenStream || localStream;
      if (!sourceStream) return;
      recorderChunks = [];
      recorder = new MediaRecorder(sourceStream, { mimeType: 'video/webm' });
      recorder.ondataavailable = (event) => {
        if (event.data.size > 0) recorderChunks.push(event.data);
      };
      recorder.onstop = async () => {
        const blob = new Blob(recorderChunks, { type: 'video/webm' });
        const form = new FormData();
        form.append('_csrf', classroomConfig.csrf);
        form.append('session_id', String(classroomConfig.sessionId));
        form.append('recording', blob, `session-${classroomConfig.sessionId}.webm`);
        const response = await fetch('live_session_recording_upload.php', { method: 'POST', body: form });
        const data = await response.json();
        if (data.ok && data.url) {
          window.location.reload();
        } else {
          alert(data.error || 'Recording upload failed.');
        }
        recorder = null;
        recordBtn.classList.remove('active');
        recordBtn.innerHTML = '<i class="fa fa-record-vinyl me-1"></i>Start Recording';
      };
      recorder.start();
      recordBtn.classList.add('active');
      recordBtn.innerHTML = '<i class="fa fa-stop me-1"></i>Stop Recording';
    } else {
      recorder.stop();
    }
  });
}

window.addEventListener('beforeunload', () => {
  navigator.sendBeacon('live_session_api.php', new Blob([JSON.stringify({
    action: 'leave',
    session_id: classroomConfig.sessionId,
    participant_key: classroomConfig.participantKey,
    csrf: classroomConfig.csrf
  })], { type: 'application/json' }));
});

async function bootstrap() {
  await api('join', { display_name: classroomConfig.displayName, role: classroomConfig.role });
  if (!classroomConfig.isTeamsRoom) {
    await startMedia();
  }
  await refreshParticipants();
  await refreshChat();
  setInterval(refreshParticipants, 3000);
  if (!classroomConfig.isTeamsRoom) {
    setInterval(handleSignals, 1500);
  }
  setInterval(refreshChat, 2000);
  setInterval(() => api('heartbeat'), 5000);
}

bootstrap().catch((error) => {
  console.error(error);
  alert(classroomConfig.isTeamsRoom
    ? 'Unable to start the Teams LMS room. Please refresh and try again.'
    : 'Unable to start the LMS classroom. Please allow camera and microphone access and confirm STUN/TURN is configured in `.env`.');
});
</script>
</body>
</html>
