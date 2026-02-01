<?php
header("Content-Type: application/json");

/*
  Dummy compiler backend
  NO real code execution
  UI testing only
*/

$questionId = intval($_POST['question_id'] ?? 0);
$code = $_POST['code'] ?? "";

/* Fake testcase data */
$testcases = [
  [
    "input" => "2 3",
    "expected" => "5"
  ],
  [
    "input" => "10 20",
    "expected" => "30"
  ]
];

/* Fake logic:
   If code contains '+' assume correct
*/
$looksCorrect = strpos($code, '+') !== false;

$results = [];
$allPassed = true;

foreach ($testcases as $t) {
  if ($looksCorrect) {
    $obtained = $t["expected"];
    $status = "PASS";
  } else {
    $obtained = "0";
    $status = "FAIL";
    $allPassed = false;
  }

  $results[] = [
    "expected" => $t["expected"],
    "output" => $obtained,
    "status" => $status
  ];
}

echo json_encode([
  "status" => $allPassed ? "success" : "failed",
  "results" => $results
]);
