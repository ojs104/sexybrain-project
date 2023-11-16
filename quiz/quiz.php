<?php
include "../connect/connect.php";
include "../connect/session.php";
include "../connect/sessionCheck.php";

$quizId = $_GET['quizId'];
$memberId = $_SESSION['memberId'];
$youId = $_SESSION['youId'];

// 퀴즈 정보 가져오기
$quizSql = "SELECT * FROM quiz WHERE quizId = '$quizId'";
$quizResult = $connect->query($quizSql);
$quizInfo = $quizResult->fetch_array(MYSQLI_ASSOC);

$rankSql = "SELECT qm.memberId, sm.youId, (q.timeLimit - qm.clearTime) as actualTime FROM quizMember qm JOIN sexyMembers sm ON qm.memberId = sm.memberId JOIN quiz q ON q.quizId = qm.quizId WHERE qm.quizId = '$quizId' AND qm.isSolved = 1 ORDER BY actualTime ASC LIMIT 3";
$result = $connect->query($rankSql);

$myTimeSql = "SELECT (q.timeLimit - qm.clearTime) as myActualTime FROM quizMember qm JOIN quiz q ON q.quizId = qm.quizId WHERE qm.quizId = '$quizId' AND qm.memberId = $memberId";
$myTimeResult = $connect->query($myTimeSql);
$myTimeRow = $myTimeResult->fetch_assoc();
$myActualTime = $myTimeRow['myActualTime'];

$myRankSql = "SELECT COUNT(*) AS myRank FROM (SELECT qm.memberId, (q.timeLimit - qm.clearTime) as actualTime FROM quizMember qm JOIN quiz q ON q.quizId = qm.quizId WHERE qm.quizId = '$quizId' AND qm.isSolved = 1 ORDER BY actualTime ASC) r WHERE actualTime <= $myActualTime";
$myRankResult = $connect->query($myRankSql);

if ($myRankResult->num_rows > 0) {
    $myRankRow = $myRankResult->fetch_assoc();
    $myRank = $myRankRow['myRank'];
} else {
    $myRank = "아직 순위가 결정되지 않았습니다.";
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>뇌섹남녀-boardWrite</title>
    <link rel="stylesheet" href="../quiz/css/quiz.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
    .answerImg {
        width: 90%;
    }

    #next {
        background-color: var(--primary-color);
        color: var(--white);
        text-align: center;
        padding: 0;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 20%;
        margin-left: 5px;
    }

    #next:hover {
        background-color: #5a68ad;
    }

    .rank__inner {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .rank__inner li {
        display: inline-block;
    }

    .rank__inner li:nth-child(1) {
        font-size: 22px;
        font-weight: 700;
    }

    .rank__inner li:nth-child(1)::before {
        content: '🥇';
    }

    .rank__inner li:nth-child(2) {
        font-size: 18px;
        font-weight: 600;
        margin-left: 15px;
    }

    .rank__inner li:nth-child(2)::before {
        content: '🥈';
    }

    .rank__inner li:nth-child(3) {
        font-size: 16px;
        font-weight: 500;
        margin-left: 15px;
    }

    .rank__inner li:nth-child(3)::before {
        content: '🥉';
    }

    .myRank {
        font-size: 20px;
        margin-left: 15px;
        background-color: #9E3436;
        padding: 0.5rem 1rem;
        border-radius: 13px;
        color: #fff;
    }
    </style>

</head>

<body>
    <div id="wrap">
        <?php include "../include/header.php" ?>
        <!-- header -->

        <main id="main">
            <section id="quiz_section">
                <div class="quiz_wrap">

                    <div class="quiz_q_wrap quiz_class">
                        <div class="quiz_timer">
                            <span id="timer"><span id="timeLeft">0:00</span></span>

                        </div>


                        <div class="q_question blind" id="modal01">
                            <div class="question_wrap">
                                <em>Q<i id="q_em">uiz</i></em>
                                <p>
                                    <?= $quizInfo['question1'] ?>
                                </p>
                            </div>
                            <div class="img_wrap">
                                <?php
                                // question2가 있을 경우 출력
                                if (!empty($quizInfo['question2'])) {
                                    echo '<div class="quiz_url">' . $quizInfo['question2'] . '</div>';
                                }

                                // question3가 있을 경우 이미지 출력
                                if (!empty($quizInfo['question3'])) {
                                    echo '<img src="' . $quizInfo['question3'] . '" alt="질문 이미지">';
                                }
                                ?>
                            </div>
                        </div>
                        <!-- 모달01 -->

                        <div class="q_question" id="modal02">
                            <div class="question_wrap">
                                <em>Q<i id="q_em">uiz</i></em>
                                <p></p>
                            </div>
                            <div class="img_wrap">
                                <img src="../quiz/img/기본.png" alt="기본사진">
                            </div>

                            <button id="startTimer">시작</button>
                        </div>
                        <!-- 모달02 -->

                    </div>
                    <form action="checkAnswer.php" method="post" class="q_answer">
                        <input type="hidden" id="quizId" name="quizId" value="<?= $quizId ?>">
                        <label for="answer">정답 : </label>
                        <input type="text" id="answer" name="answer">
                        <input type="submit" id="submit" value="제출">
                        <input type="next" id="next" value="다음">
                    </form>
                    <button id="likeButton" data-quizid="<?= $quizId ?>">좋아요<em>❤</em></button>
                </div>

                <div class="rank__wrap">
                    <div class="rank__inner">
                        <ul>
                            <?php
                            if ($result->num_rows > 0) {
                                $rank = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<li><span>" . $rank . "위! " . $row["youId"] . " 님 " . $row["actualTime"] . "초</span></li>";
                                    $rank++;
                                }
                                echo "<li class='myRank'><span>나의 순위: " . $myRank . "위</span></li>";
                            } else {
                                echo "<p>아직 문제를 푼 사람이 없습니다. 지금 풀면 여러분이 1등🥇!!</p>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- rank -->
            </section>
        </main>
        <!-- main -->

        <?php include "../include/footer.php" ?>
        <!-- footer -->
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal__inner">
                <span class="close">&times;</span>
                <p id="result"></p>
                <p class="m_img"><img src=<?= $quizInfo['descImg'] ?> alt="질문 이미지" id="answerText"
                        class="answerImg blind"></p>
                <p class="hint blind">
                    <?= $quizInfo['hint'] ?>
                </p>
                <div class="m_wrap2">
                    <button id="showAnswer" class="blind">정답 보기</button>
                    <button id="showHint" class="blind">힌트 보기</button>

                    <button id="showRetry" class="blind">다시 풀기</button>
                    <button id="showNext" class="blind">다음 문제</button> <!-- 새로 추가 -->
                    <a href="quizHome.php">목록으로</a>
                </div>
            </div>
        </div>
    </div>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        let timeLimit = <?= $quizInfo['timeLimit'] ?>;
        let timerInterval;

        // 타이머 업데이트 함수
        function updateTimer() {
            let minutes = Math.floor(timeLimit / 60);
            let seconds = timeLimit % 60;

            // 시간을 2자리 숫자로 표시
            let minutesStr = (minutes < 10) ? "0" + minutes : minutes;
            let secondsStr = (seconds < 10) ? "0" + seconds : seconds;

            // 시간 표시 업데이트
            $('#timeLeft').text(minutesStr + ":" + secondsStr);

            // 시간 감소
            timeLimit--;

            // 시간 종료 시 처리
            if (timeLimit < 0) {
                clearInterval(timerInterval);
                // 여기에서 시간이 종료되었을 때 실행해야 할 코드를 추가할 수 있습니다.
                $('#result').text("시간이 종료되었습니다.");
                $('#showAnswer').removeClass('blind');
                $('#showHint').removeClass('blind');
                $('#showRetry').removeClass('blind');
                $('#showNext').removeClass('blind');
                $('#answerText').text(result.answer);
                $('#modal').removeClass('blind');
                $('#modal').css('display', 'block');
            }
        }

        // 시작 버튼 클릭 이벤트 핸들러
        $('#startTimer').click(function() {
            // 시작 버튼을 누르면 타이머 시작
            timerInterval = setInterval(updateTimer, 1000);
            // 버튼 상태 변경
            $('#startTimer').addClass('blind');

            $('#modal02').addClass('blind');
            $('#modal01').removeClass('blind');
        });


        // 다음 버튼 클릭 이벤트 핸들러
        let totalQuizId = 3; // 총 문제 개수를 여기에 입력하세요.

        $('#next').click(function() {
            let currentQuizId = <?= $quizId ?>;
            if (currentQuizId >= totalQuizId) {
                alert('마지막 문제입니다.');
            } else {
                let nextQuizId = currentQuizId + 1;
                location.href = 'quiz.php?quizId=' + nextQuizId;
            }
        });


        // 제출 버튼 클릭 이벤트 핸들러
        $('#submit').click(function(e) {
            e.preventDefault();

            let quizId = $('#quizId').val();
            let answer = $('#answer').val();

            $.ajax({
                url: 'checkAnswer.php',
                type: 'post',
                data: {
                    quizId: quizId,
                    answer: answer
                },
                success: function(response) {
                    let result = JSON.parse(response);

                    if (result.correct) {
                        $('#result').text("정답입니다!");
                    } else {
                        $('#result').text("틀렸습니다.");
                        $('#showAnswer').removeClass('blind');
                        $('#showHint').removeClass('blind');
                        $('#showRetry').removeClass('blind');
                        $('#showNext').removeClass('blind');
                        $('#answerText').text(result.answer);
                    }

                    $('#modal').css('display', 'block');
                }
            });
        });
    });


    $('#submit').click(function(e) {
        e.preventDefault();

        let quizId = $('#quizId').val();
        let answer = $('#answer').val();

        $.ajax({
            url: 'checkAnswer.php',
            type: 'post',
            data: {
                quizId: quizId,
                answer: answer
            },
            success: function(response) {
                let result = JSON.parse(response);

                if (result.correct) {
                    $('#result').text("정답입니다!");
                } else {
                    $('#result').text("틀렸습니다.");
                    $('#showAnswer').removeClass('blind');
                    $('#showHint').removeClass('blind');
                    $('#showRetry').removeClass('blind');
                    $('#showNext').removeClass('blind');
                    $('#answerText').text(result.answer);
                }

                $('#modal').css('display', 'block');
            }
        });
    });
    //정답보기 버튼 클릭 시
    $('#showAnswer').click(function() {
        $('#answerText').removeClass('blind');
    });

    $('#showHint').click(function() {
        $('.hint').removeClass('blind');
    });

    $('#showRetry').click(function() {
        location.reload();
    });

    $('#showNext').click(function() {
        let currentQuizId = <?= $quizId ?>;
        let nextQuizId = currentQuizId + 1;
        location.href = 'quiz.php?quizId=' + nextQuizId;
    });

    $('#go__list').click(function() {
        location.href = 'quizHome.php';
    });

    $('.close').click(function() {
        $('#modal').css('display', 'none');
    });


    // 퀴즈가 이미 좋아요된 상태인지 확인하고 CSS를 업데이트합니다.
    let quizId = $('#likeButton').data("quizid");
    let likedStatus = localStorage.getItem('liked_' + quizId);

    if (likedStatus === 'liked') {
        $('#likeButton').addClass('liked');
    }

    // 좋아요 버튼 클릭 이벤트 핸들러
    $('#likeButton').click(function() {
        let quizId = $(this).data("quizid");

        $.ajax({
            url: 'likeQuiz.php',
            type: 'post',
            data: {
                quizId: quizId
            },
            success: function(response) {
                if (response === 'liked') {
                    // 좋아요가 성공적으로 추가된 경우
                    $('#likeButton').addClass('liked');
                    // 로컬 저장소에 좋아요 상태 저장
                    localStorage.setItem('liked_' + quizId, 'liked');
                } else if (response === 'already_liked') {
                    // 이미 좋아요가 추가된 경우
                    $('#likeButton').removeClass('liked');
                    // 로컬 저장소에서 좋아요 상태 제거
                    localStorage.removeItem('liked_' + quizId);
                }
            }
        });


    });
    </script>

</body>

</html>