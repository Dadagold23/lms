import pymysql, os, sys

# Read .env
env = {}
with open('.env') as f:
    for line in f:
        line = line.strip()
        if '=' in line and not line.startswith('#'):
            k, v = line.split('=', 1)
            env[k.strip()] = v.strip()

conn = pymysql.connect(
    host=env.get('DB_HOST','localhost'),
    user=env.get('DB_USER','root'),
    password=env.get('DB_PASS',''),
    database=env.get('DB_NAME','mirror_age_lms'),
    charset='utf8mb4'
)
cur = conn.cursor()
cur.execute("SELECT COALESCE(MAX(id),0)+1 FROM lms_lessons")
lid = cur.fetchone()[0]
ts = '2026-04-15 08:00:00'

def L(cid, title, content, sort):
    global lid
    cur.execute(
        "INSERT IGNORE INTO lms_lessons (id,course_id,title,content,sort_order,is_published,created_at) VALUES (%s,%s,%s,%s,%s,1,%s)",
        (lid, cid, title, content, sort, ts)
    )
    lid += 1

# ═══════════════════════════════════════════════════════
# ARTIFICIAL INTELLIGENCE — course_id=18
# ═══════════════════════════════════════════════════════
L(18,"Introduction to Artificial Intelligence",
"""## What is Artificial Intelligence?

Artificial Intelligence (AI) is the simulation of human intelligence processes by machines. These processes include learning, reasoning, problem-solving, perception, and language understanding.

## Types of AI

By Capability:
- Narrow AI (ANI): Designed for one specific task (Siri, chess engines, spam filters)
- General AI (AGI): Human-level intelligence across all domains (not yet achieved)
- Super AI (ASI): Surpasses human intelligence in all areas (theoretical)

By Approach:
- Rule-based AI: Expert systems with hand-coded rules
- Machine Learning: Learns patterns from data
- Deep Learning: Neural networks with many layers
- Generative AI: Creates new content (text, images, code)

## AI History Timeline

- 1950: Alan Turing proposes the Turing Test
- 1956: Term AI coined at Dartmouth Conference
- 1997: IBM Deep Blue beats chess world champion
- 2012: Deep learning revolution (AlexNet wins ImageNet)
- 2016: AlphaGo beats world Go champion
- 2017: Transformer architecture introduced
- 2022: ChatGPT launches, mainstream AI adoption begins
- 2024: GPT-4o, Gemini, Claude 3 — multimodal AI

## AI Applications in Nigeria and Africa

- Agriculture: Crop disease detection from smartphone photos
- Healthcare: Malaria diagnosis from blood smear images
- Finance: Fraud detection, credit scoring for the unbanked
- Education: Personalised learning, automated grading
- Language: NLP for Yoruba, Igbo, Hausa, Pidgin

## Setting Up Your AI Environment

```bash
pip install numpy pandas scikit-learn tensorflow torch transformers openai
```

## Practical Task

Research 3 AI applications currently being used in Nigerian companies or government. For each: describe the problem being solved, the AI approach used, and the impact. Write a 1-page report.

## Self-Check
1. What is the difference between Narrow AI and General AI?
2. What was the significance of the 2012 AlexNet breakthrough?
3. Name 3 AI applications relevant to the Nigerian context.""",
1)

L(18,"Machine Learning Foundations",
"""## The Machine Learning Paradigm

Traditional programming: Rules + Data = Output
Machine Learning: Data + Output = Rules

Instead of writing rules, we feed examples to an algorithm that learns the rules automatically.

## Types of Machine Learning

Supervised Learning: Learn from labelled examples
- Classification: Predict a category (spam/not spam)
- Regression: Predict a number (house price)

Unsupervised Learning: Find patterns in unlabelled data
- Clustering: Group similar items (customer segments)
- Dimensionality Reduction: Compress data (PCA)

Reinforcement Learning: Learn by trial and error with rewards
- Agent takes actions, receives rewards or penalties
- Applications: Game playing, robotics, trading

## The ML Workflow

```python
from sklearn.datasets import load_iris
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score, classification_report

X, y = load_iris(return_X_y=True)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

scaler = StandardScaler()
X_train = scaler.fit_transform(X_train)
X_test  = scaler.transform(X_test)

model = SVC(kernel='rbf', C=1.0)
model.fit(X_train, y_train)

y_pred = model.predict(X_test)
print(f'Accuracy: {accuracy_score(y_test, y_pred):.3f}')
print(classification_report(y_test, y_pred))
```

## Overfitting vs Underfitting

- Underfitting: Model too simple, high bias, poor on training and test data
- Overfitting: Model too complex, high variance, great on training but poor on test
- Good fit: Low bias and low variance, generalises well

Solutions:
- Underfitting: More features, more complex model, more training
- Overfitting: More data, regularisation, dropout, cross-validation

## Practical Task

Build a spam email classifier using the SMS Spam Collection dataset from UCI. Try 3 algorithms: Naive Bayes, Logistic Regression, Random Forest. Compare accuracy, precision, recall, and F1.

## Self-Check
1. What is the difference between supervised and unsupervised learning?
2. What is overfitting and how do you prevent it?
3. What is the difference between classification and regression?""",
2)

L(18,"Neural Networks & Deep Learning",
"""## What is a Neural Network?

A neural network is a computational model inspired by the human brain. It consists of layers of interconnected nodes (neurons) that transform input data into output predictions.

## Architecture

- Input Layer: Receives raw features
- Hidden Layers: Learn intermediate representations
- Output Layer: Produces final prediction
- Weights: Parameters learned during training
- Activation Functions: Introduce non-linearity

## Activation Functions

- ReLU: max(0, x) — most common for hidden layers
- Sigmoid: 1/(1+e^-x) — binary classification output
- Softmax: Multi-class classification output
- Tanh: Range -1 to 1, used in RNNs

## Building a Neural Network with Keras

```python
import tensorflow as tf
from tensorflow import keras

(X_train, y_train), (X_test, y_test) = keras.datasets.mnist.load_data()
X_train = X_train.reshape(-1, 784) / 255.0
X_test  = X_test.reshape(-1, 784) / 255.0

model = keras.Sequential([
    keras.layers.Dense(256, activation='relu', input_shape=(784,)),
    keras.layers.Dropout(0.3),
    keras.layers.Dense(128, activation='relu'),
    keras.layers.Dropout(0.3),
    keras.layers.Dense(10, activation='softmax')
])

model.compile(optimizer='adam', loss='sparse_categorical_crossentropy', metrics=['accuracy'])
history = model.fit(X_train, y_train, epochs=10, batch_size=32, validation_split=0.1)

test_loss, test_acc = model.evaluate(X_test, y_test)
print(f'Test accuracy: {test_acc:.4f}')
```

## Backpropagation

1. Forward pass: Compute predictions
2. Calculate loss (error)
3. Backward pass: Compute gradients using chain rule
4. Update weights: w = w - learning_rate * gradient

## Practical Task

Build a neural network to classify handwritten digits (MNIST). Experiment with layers, neurons, dropout rates, and learning rates. Plot training and validation accuracy curves. Achieve at least 98% test accuracy.

## Self-Check
1. What is the role of activation functions in neural networks?
2. What is dropout and why does it help prevent overfitting?
3. Explain backpropagation in simple terms.""",
3)

L(18,"Computer Vision with CNNs",
"""## What is Computer Vision?

Computer vision enables machines to interpret and understand visual information from images and videos. It is one of the most successful applications of deep learning.

## Convolutional Neural Networks (CNNs)

CNNs are designed specifically for image data. They use convolutional layers to automatically learn spatial features.

Key Components:
- Convolutional Layer: Applies filters to detect features (edges, textures, shapes)
- Pooling Layer: Reduces spatial dimensions (Max Pooling)
- Flatten Layer: Converts 2D feature maps to 1D vector
- Dense Layer: Final classification

```python
import tensorflow as tf
from tensorflow import keras

model = keras.Sequential([
    keras.layers.Conv2D(32, (3,3), activation='relu', input_shape=(224,224,3)),
    keras.layers.MaxPooling2D(2,2),
    keras.layers.Conv2D(64, (3,3), activation='relu'),
    keras.layers.MaxPooling2D(2,2),
    keras.layers.Conv2D(128, (3,3), activation='relu'),
    keras.layers.MaxPooling2D(2,2),
    keras.layers.Flatten(),
    keras.layers.Dense(512, activation='relu'),
    keras.layers.Dropout(0.5),
    keras.layers.Dense(10, activation='softmax')
])
```

## Transfer Learning

```python
base_model = keras.applications.MobileNetV2(weights='imagenet', include_top=False, input_shape=(224,224,3))
base_model.trainable = False

model = keras.Sequential([
    base_model,
    keras.layers.GlobalAveragePooling2D(),
    keras.layers.Dense(256, activation='relu'),
    keras.layers.Dense(num_classes, activation='softmax')
])
```

Popular pre-trained models: VGG16, ResNet50, InceptionV3, EfficientNet, MobileNet

## Real-World Applications

- Medical imaging: Detect malaria, tuberculosis, cancer from scans
- Agriculture: Identify crop diseases from leaf photos
- Security: Face recognition, object detection
- Autonomous vehicles: Lane detection, pedestrian detection

## Practical Task

Build a crop disease classifier using transfer learning (MobileNetV2). Dataset: PlantVillage on Kaggle. Train to classify 5 disease types. Achieve 90%+ validation accuracy. Build a simple interface where a farmer can upload a leaf photo and get a diagnosis.

## Self-Check
1. What is the purpose of a convolutional layer?
2. What is transfer learning and why is it useful?
3. Name 3 real-world applications of computer vision in Africa.""",
4)

L(18,"Natural Language Processing (NLP)",
"""## What is NLP?

Natural Language Processing (NLP) enables computers to understand, interpret, and generate human language.

## NLP Pipeline

1. Text Preprocessing: Tokenisation, lowercasing, punctuation removal
2. Stop Word Removal: Remove common words (the, is, at)
3. Stemming/Lemmatisation: Reduce words to root form
4. Feature Extraction: Convert text to numbers
5. Modelling: Apply ML/DL model

## Text Preprocessing

```python
import re, nltk
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer

def preprocess(text):
    text = text.lower()
    text = re.sub(r'[^a-z0-9\\s]', '', text)
    tokens = word_tokenize(text)
    stop_words = set(stopwords.words('english'))
    tokens = [t for t in tokens if t not in stop_words]
    lemmatizer = WordNetLemmatizer()
    return ' '.join([lemmatizer.lemmatize(t) for t in tokens])
```

## TF-IDF Classification

```python
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

vectorizer = TfidfVectorizer(max_features=5000, ngram_range=(1,2))
X_train_vec = vectorizer.fit_transform(X_train)
X_test_vec  = vectorizer.transform(X_test)

model = LogisticRegression()
model.fit(X_train_vec, y_train)
```

## Transformers & BERT

```python
from transformers import pipeline

sentiment = pipeline('sentiment-analysis')
result = sentiment('This course is absolutely amazing!')
print(result)  # [{'label': 'POSITIVE', 'score': 0.9998}]

ner = pipeline('ner', grouped_entities=True)
result = ner('Dangote Group is headquartered in Lagos, Nigeria.')
```

## NLP for African Languages

Projects: Masakhane (masakhane.io), AfriSenti, Yoruba/Hausa/Igbo datasets on HuggingFace

## Practical Task

Build a sentiment analyser for Nigerian social media comments. Collect 500 comments about a Nigerian brand. Preprocess the text. Train a classifier (Logistic Regression + TF-IDF). Evaluate with F1 score.

## Self-Check
1. What is the difference between stemming and lemmatisation?
2. What is TF-IDF and why is it better than simple word counts?
3. What is a Transformer model and what problem does it solve?""",
5)

L(18,"Generative AI & Large Language Models",
"""## What is Generative AI?

Generative AI creates new content — text, images, audio, video, code — that did not exist before. It is powered by large models trained on massive datasets.

## Large Language Models (LLMs)

LLMs are neural networks trained on billions of text tokens. They learn to predict the next token, enabling coherent text generation.

Key LLMs:
- GPT-4o / GPT-5 (OpenAI): Most capable general-purpose LLM
- Claude 3.5 (Anthropic): Strong reasoning and safety
- Gemini 1.5 (Google): Multimodal, long context
- Llama 3 (Meta): Open-source, can run locally

## Using OpenAI Responses API

```python
from openai import OpenAI

client = OpenAI(api_key='your-api-key')

# Responses API (gpt-5-nano)
response = client.responses.create(
    model='gpt-5-nano',
    instructions='You are a helpful tutor for Nigerian students.',
    input='Explain machine learning in simple terms.',
    store=True
)
print(response.output_text)
```

## Prompt Engineering

- Zero-shot: Ask directly without examples
- Few-shot: Provide 2-3 examples before the question
- Chain-of-thought: Ask the model to think step by step
- Role prompting: Assign a persona (You are an expert...)
- Structured output: Ask for JSON, tables, or specific formats

## RAG (Retrieval-Augmented Generation)

RAG combines LLMs with a knowledge base:
1. Store documents in a vector database (Pinecone, ChromaDB)
2. Convert query to embedding
3. Retrieve most similar documents
4. Feed documents + query to LLM
5. LLM generates answer grounded in your documents

## Practical Task

Build an AI tutor chatbot for one of your courses using the OpenAI API. Features: (1) Answer questions about course content, (2) Generate practice questions, (3) Explain concepts at different difficulty levels.

## Self-Check
1. What is the key innovation of the Transformer architecture?
2. What is prompt engineering and why does it matter?
3. What is RAG and when would you use it instead of fine-tuning?""",
6)

L(18,"AI Ethics, Safety & Responsible AI",
"""## Why AI Ethics Matters

AI systems can cause significant harm if not designed and deployed responsibly.

## Key AI Ethics Principles

- Fairness: AI should not discriminate based on protected characteristics
- Transparency: Users should understand how AI makes decisions
- Accountability: Someone must be responsible when AI causes harm
- Privacy: AI should not violate users' privacy
- Safety: AI systems should be reliable and not cause unintended harm
- Beneficence: AI should benefit humanity

## AI Bias

Sources of bias:
- Historical bias: Training data reflects past discrimination
- Representation bias: Underrepresented groups in training data
- Measurement bias: Proxy variables correlating with protected attributes

Example: A hiring AI trained on historical data may discriminate against women if historically fewer women were hired.

## Detecting Bias

```python
from sklearn.metrics import classification_report

# Check performance by demographic group
for group in df['gender'].unique():
    mask = df['gender'] == group
    print(f'--- {group} ---')
    print(classification_report(y_test[mask], y_pred[mask]))

# Fairness metrics
from fairlearn.metrics import demographic_parity_difference
dpd = demographic_parity_difference(y_test, y_pred, sensitive_features=df['gender'])
print(f'Demographic Parity Difference: {dpd:.3f}')
```

## AI Safety

- Alignment: Ensuring AI pursues human-intended goals
- Robustness: AI should work reliably in unexpected situations
- Adversarial attacks: Inputs designed to fool AI models
- Hallucination: LLMs generating false but confident information

## AI Regulation

- EU AI Act (2024): Risk-based regulation of AI systems
- Nigeria: NITDA AI Policy Framework
- US: Executive Order on AI Safety

## Practical Task

Audit a machine learning model for bias. Train a loan approval model. Check if the model has different approval rates for different demographic groups. Calculate fairness metrics. Propose 3 interventions to reduce bias.

## Self-Check
1. What is algorithmic bias and how does it arise?
2. What is the difference between AI safety and AI ethics?
3. What is the EU AI Act and what does it regulate?""",
7)

L(18,"Capstone: AI Application Project",
"""## Final Project Brief

Design and build a complete AI application that solves a real problem relevant to Nigeria or Africa.

## Project Options (Choose One)

### Option A: AI Health Assistant
A chatbot that helps Nigerians understand medical symptoms and find nearby healthcare facilities.
- NLP for symptom extraction from natural language
- Medical knowledge base (RAG)
- Multilingual support (English + Pidgin)
- Integration with Google Maps API for facility lookup

### Option B: Agricultural AI Advisor
An AI system that helps Nigerian farmers make better decisions.
- Crop disease detection from photos (CNN)
- Weather-based planting recommendations
- Market price prediction (time series)
- Voice interface in Hausa or Yoruba

### Option C: Financial Inclusion AI
An AI system for credit scoring of unbanked Nigerians.
- Alternative data features (mobile money, airtime usage)
- Fairness-aware model (no discrimination by ethnicity/gender)
- Explainable AI (SHAP values for each decision)
- Simple mobile interface

## Technical Requirements

- At least 2 AI/ML components
- Training data: minimum 1,000 examples
- Model evaluation with appropriate metrics
- Bias audit with fairness metrics
- Deployed application (Streamlit, Flask, or mobile)
- API integration (OpenAI or similar)

## Deliverables

1. GitHub repository with clean, documented code
2. Jupyter Notebook with model development
3. Deployed application (live URL)
4. 15-minute demo presentation
5. Technical report (5 pages)
6. Ethical impact assessment

## Evaluation Criteria
- Problem relevance and impact (20%)
- Technical implementation quality (30%)
- Model performance (20%)
- Responsible AI practices (15%)
- Presentation and documentation (15%)

## Self-Check
1. Does your application solve a real problem for real people?
2. Have you tested your model for bias and fairness?
3. Can a non-technical user understand and use your application?""",
8)

print("AI: 8 lessons done")
conn.commit()
conn.close()
