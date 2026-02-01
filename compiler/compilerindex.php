<?php
session_start();

$user_id   = $_SESSION['user_id']   ?? 1;
$user_name = $_SESSION['user_name'] ?? 'Student';
$course    = "DSA Basics";

$conn = new mysqli("localhost", "root", "", "coding");
if ($conn->connect_error) die("DB connection failed");

$questionId = intval($_GET['question_id'] ?? 0);
$lang = strtoupper($_GET['lang'] ?? 'C');
if ($questionId <= 0) die("Invalid question");

$q = $conn->query("SELECT * FROM questions WHERE question_id=$questionId");
$question = $q->fetch_assoc();

$v = $conn->query("SELECT * FROM question_variants 
                   WHERE question_id=$questionId 
                   AND language='".$conn->real_escape_string($lang)."'");
$variant = $v->fetch_assoc();

$functionTemplate =
trim(rtrim($variant['function_signature'], ';')) . "\n{\n    \n}";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($question['title']) ?></title>

<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
/* ---- STYLING FROZEN (UNCHANGED) ---- */
body{margin:0;font-family:"IBM Plex Sans",sans-serif;background:#f7f7fb;font-size:15.5px}
.topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 36px;background:#fff;border-bottom:1px solid #e5e7eb}
.top-left{display:flex;align-items:center;gap:14px}
.back-btn{text-decoration:none;font-size:20px;color:#444}
.title{font-size:22px;font-weight:700}
.meta{font-size:14px;color:#555;margin-top:4px}
.top-right{display:flex;align-items:center;gap:20px;font-size:14.5px;font-weight:500}
.container{max-width:1450px;margin:26px auto;padding:0 32px; display:flex;gap:16px}
.left,.right{background:#fff;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
.left{flex:1.15;padding:30px}
.right{flex:1.45;padding:28px;display:flex;flex-direction:column;gap:14px}
.left p,.output,textarea{font-size:15.8px}
.rounded-table{width:100%;border-collapse:separate;border-spacing:0;border-radius:14px;overflow:hidden;margin-top:12px}
.rounded-table th,.rounded-table td{padding:13px;border-bottom:1px solid #e5e7eb;font-family:monospace}
.rounded-table th{background:#f3f4f6;font-weight:600}
.rounded-table tr:last-child td{border-bottom:none}
textarea{width:100%;font-family:monospace;border-radius:12px;border:1px solid #d1d5db;padding:14px;resize:none}
#code-editor{height:230px}
#customInput{height:80px}
pre{background:#f3f4f6;padding:14px;border-radius:12px;font-family:monospace;font-size:15.5px;margin-top:10px}
button{padding:12px 20px;border:none;border-radius:12px;background:#6c63ff;color:#fff;font-weight:600;cursor:pointer;font-size:15.5px}
button:hover{background:#5b54d6}
.checkbox{display:flex;align-items:center;gap:8px;font-size:15px}
.output{background:#f3f4f6;padding:14px;border-radius:12px;font-family:monospace}
.pass{color:green;font-weight:600}
.fail{color:red;font-weight:600}
.row-between{display:flex;align-items:center;justify-content:space-between}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
  <div>
    <div class="top-left">
      <a class="back-btn" href="../questions.php?lang=<?= $lang ?>">←</a>
      <div class="title"><?= htmlspecialchars($question['title']) ?></div>
    </div>
    <div class="meta">
      Course: <b><?= $course ?></b> |
      Language: <b><?= $lang ?></b>
    </div>
  </div>

  <div class="top-right">
    <div>👤 <?= htmlspecialchars($user_name) ?></div>
    <div id="timer">⏱ 00:00</div>
  </div>
</div>

<div class="container">

<!-- LEFT -->
<div class="left">
  <h2>Description</h2>
  <p><?= htmlspecialchars($question['description']) ?></p>

  <h3>Constraints</h3>
  <p><?= htmlspecialchars($question['constraints'] ?? '1 ≤ N ≤ 10⁵') ?></p>

  <h3>Sample Input & Output</h3>
  <table class="rounded-table">
    <tr><th>Input</th><th>Output</th></tr>
    <tr><td>1 2 3</td><td>6</td></tr>
    <tr><td>10 20 30</td><td>60</td></tr>
  </table>

  <div class="checkbox" style="margin-top:14px">
    <input type="checkbox" id="starterToggle">
    <label for="starterToggle">Show Starter Code</label>
  </div>

  <pre id="starterCode" style="display:none"><?= htmlspecialchars($variant['starter_code']) ?></pre>
</div>

<!-- RIGHT -->
<div class="right">

  <textarea id="code-editor"><?= htmlspecialchars($functionTemplate) ?></textarea>

  <div class="row-between">
    <div class="checkbox">
      <input type="checkbox" id="customToggle">
      <label for="customToggle">Enable Custom Input</label>
    </div>
    <button id="runBtn">Run Code</button>
  </div>

  <textarea id="customInput" style="display:none" placeholder="Enter custom input"></textarea>

  <div class="output" id="output">Output will appear here</div>

  <table class="rounded-table">
    <tr><th>Expected Output</th><th>Obtained Output</th></tr>
    <tr><td>6</td><td id="got">-</td></tr>
  </table>
  <div id="status"></div>

</div>
</div>

<script>
/* TIMER PERSISTENCE */
const key = "timer_q_<?= $questionId ?>";
let seconds = parseInt(sessionStorage.getItem(key)) || 0;

setInterval(()=>{
  seconds++;
  sessionStorage.setItem(key, seconds);
  timer.textContent =
    "⏱ " +
    String(Math.floor(seconds/60)).padStart(2,'0') +
    ":" +
    String(seconds%60).padStart(2,'0');
},1000);

/* TOGGLES */
starterToggle.onchange = e =>
  starterCode.style.display = e.target.checked ? "block" : "none";

customToggle.onchange = e =>
  customInput.style.display = e.target.checked ? "block" : "none";

/* DUMMY RUN */
runBtn.onclick = ()=>{
  const ok = codeEditor.value.includes('+');
  output.textContent = ok ? "All testcases passed" : "Some testcases failed";
  got.textContent = ok ? "6" : "0";
  status.innerHTML = ok
    ? "<span class='pass'>✓ PASS</span>"
    : "<span class='fail'>✗ FAIL</span>";
};
</script>

</body>
</html>
