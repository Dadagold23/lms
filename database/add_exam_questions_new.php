<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/db.php';
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$ts = '2026-04-15 08:00:00';
$qid = (int)$pdo->query("SELECT COALESCE(MAX(id),0)+1 FROM lms_exam_questions")->fetchColumn();
$q = $pdo->prepare("INSERT IGNORE INTO lms_exam_questions (id,exam_id,question,option_a,option_b,option_c,option_d,correct_option,marks,created_at) VALUES (?,?,?,?,?,?,?,?,1,?)");

function Q(PDOStatement $q,int &$qid,int $eid,string $question,string $a,string $b,string $c,string $d,string $correct,string $ts):void{
    $q->execute([$qid++,$eid,$question,$a,$b,$c,$d,$correct,$ts]);
}

/* ═══════════════════════════════════════════════════════
   EXAM 61: Data Science
═══════════════════════════════════════════════════════ */
Q($q,$qid,61,"What does the acronym EDA stand for in data science?","Exploratory Data Analysis","Extended Data Architecture","Evaluated Data Algorithm","Extracted Data Application","A",$ts);
Q($q,$qid,61,"Which Python library is primarily used for data manipulation and analysis?","NumPy","Matplotlib","pandas","Seaborn","C",$ts);
Q($q,$qid,61,"What is the purpose of the train/test split in machine learning?","To speed up training","To evaluate model performance on unseen data","To reduce the dataset size","To clean the data","B",$ts);
Q($q,$qid,61,"Which metric measures the proportion of variance explained by a regression model?","MAE","RMSE","R-squared (R2)","MAPE","C",$ts);
Q($q,$qid,61,"What does 'data wrangling' refer to?","Visualising data","Collecting data from APIs","Cleaning and transforming raw data into a usable format","Training machine learning models","C",$ts);
Q($q,$qid,61,"Which of the following is NOT a measure of central tendency?","Mean","Median","Standard Deviation","Mode","C",$ts);
Q($q,$qid,61,"What is a p-value in hypothesis testing?","The probability that the null hypothesis is true","The probability of observing results as extreme as the data, assuming the null hypothesis is true","The significance level","The test statistic","B",$ts);
Q($q,$qid,61,"Which tool is used to build interactive data dashboards in Python?","Matplotlib","Streamlit","NumPy","scikit-learn","B",$ts);
Q($q,$qid,61,"What are the 5 Vs of Big Data?","Volume, Velocity, Variety, Veracity, Value","Volume, Vision, Variety, Veracity, Value","Volume, Velocity, Variety, Validity, Value","Volume, Velocity, Vision, Veracity, Value","A",$ts);
Q($q,$qid,61,"What is the main advantage of Apache Spark over traditional Hadoop MapReduce?","It is free to use","It processes data in-memory, making it up to 100x faster","It requires less storage","It supports more programming languages","B",$ts);

/* ═══════════════════════════════════════════════════════
   EXAM 62: Artificial Intelligence
═══════════════════════════════════════════════════════ */
Q($q,$qid,62,"What type of AI is designed for one specific task, such as a spam filter?","General AI (AGI)","Super AI (ASI)","Narrow AI (ANI)","Symbolic AI","C",$ts);
Q($q,$qid,62,"Which year was the term 'Artificial Intelligence' first coined?","1950","1956","1969","1980","B",$ts);
Q($q,$qid,62,"What is the activation function most commonly used in hidden layers of neural networks?","Sigmoid","Tanh","Softmax","ReLU","D",$ts);
Q($q,$qid,62,"What is transfer learning in the context of deep learning?","Training a model from scratch on a new dataset","Using a pre-trained model as a starting point for a new task","Transferring data between servers","Moving a model from development to production","B",$ts);
Q($q,$qid,62,"What does NLP stand for?","Neural Learning Process","Natural Language Processing","Network Layer Protocol","Numerical Learning Pipeline","B",$ts);
Q($q,$qid,62,"What is the key innovation of the Transformer architecture introduced in 2017?","Convolutional layers for text","Self-attention mechanism allowing each token to attend to all others","Recurrent connections for sequence processing","Pooling layers for dimensionality reduction","B",$ts);
Q($q,$qid,62,"What is 'hallucination' in the context of Large Language Models?","When the model crashes","When the model generates false but confident information","When the model is too slow","When the model refuses to answer","B",$ts);
Q($q,$qid,62,"What is RAG in AI?","Random Aggregation Generator","Retrieval-Augmented Generation — combining LLMs with a knowledge base","Recurrent Attention Gate","Regularised Activation Graph","B",$ts);
Q($q,$qid,62,"What is algorithmic bias in AI?","When an algorithm runs too slowly","When an AI system produces systematically unfair outcomes due to biased training data or design","When an algorithm has too many parameters","When an AI model overfits the training data","B",$ts);
Q($q,$qid,62,"Which framework is most commonly used for building neural networks in Python?","scikit-learn","pandas","TensorFlow/Keras","Matplotlib","C",$ts);

/* ═══════════════════════════════════════════════════════
   EXAM 63: Machine Learning
═══════════════════════════════════════════════════════ */
Q($q,$qid,63,"What is the difference between supervised and unsupervised learning?","Supervised uses more data","Supervised learns from labelled examples; unsupervised finds patterns in unlabelled data","Supervised is faster","Unsupervised requires more computing power","B",$ts);
Q($q,$qid,63,"What is overfitting in machine learning?","When a model is too simple to learn the data","When a model performs well on training data but poorly on new data","When a model takes too long to train","When a model has too few parameters","B",$ts);
Q($q,$qid,63,"What does SMOTE stand for and what is it used for?","Supervised Model Optimisation Technique — for speeding up training","Synthetic Minority Over-sampling Technique — for handling imbalanced datasets","Standard Model Output Testing — for evaluation","Stochastic Model Optimisation Training — for gradient descent","B",$ts);
Q($q,$qid,63,"Which metric is most appropriate for evaluating a fraud detection model?","Accuracy","Mean Squared Error","Recall (to minimise missed fraud cases)","R-squared","C",$ts);
Q($q,$qid,63,"What is the purpose of cross-validation?","To split data into training and test sets","To get a more reliable estimate of model performance by training on multiple data splits","To clean the training data","To reduce the number of features","B",$ts);
Q($q,$qid,63,"What is the 'elbow method' used for in K-Means clustering?","Determining the optimal learning rate","Finding the optimal number of clusters (k)","Selecting the best features","Evaluating model accuracy","B",$ts);
Q($q,$qid,63,"What does PCA stand for and what does it do?","Principal Component Analysis — reduces dimensionality while retaining maximum variance","Predictive Clustering Algorithm — groups similar data points","Polynomial Coefficient Adjustment — tunes model parameters","Probabilistic Classification Approach — classifies data probabilistically","A",$ts);
Q($q,$qid,63,"What is data drift in the context of deployed ML models?","When training data is corrupted","When the statistical properties of production data change over time, degrading model performance","When the model's code has a bug","When the model is retrained too frequently","B",$ts);
Q($q,$qid,63,"What is MLflow used for?","Building neural networks","Tracking ML experiments, parameters, metrics, and models","Deploying models to mobile devices","Cleaning and preprocessing data","B",$ts);
Q($q,$qid,63,"What is the difference between L1 (Lasso) and L2 (Ridge) regularisation?","L1 is faster; L2 is more accurate","L1 can reduce some coefficients to exactly zero (feature selection); L2 shrinks all coefficients but rarely to zero","L1 is for regression; L2 is for classification","L1 uses squared penalties; L2 uses absolute value penalties","B",$ts);

// Update exam total_questions
foreach ([61,62,63] as $eid) {
    $cnt = (int)$pdo->query("SELECT COUNT(*) FROM lms_exam_questions WHERE exam_id={$eid}")->fetchColumn();
    $pdo->exec("UPDATE lms_exams SET total_questions={$cnt}, total_marks={$cnt} WHERE id={$eid}");
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo "Exam questions inserted\n";
echo "Questions per exam:\n";
foreach ([61,62,63] as $eid) {
    echo "  Exam {$eid}: ".$pdo->query("SELECT COUNT(*) FROM lms_exam_questions WHERE exam_id={$eid}")->fetchColumn()." questions\n";
}
