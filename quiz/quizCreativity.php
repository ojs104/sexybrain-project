<?php

include "../connect/connect.php";
include "../connect/session.php";

$quizSql = "SELECT * FROM quiz WHERE cate = '창의력' ORDER BY quizId DESC";
$quizResult = $connect->query($quizSql);

?>

<!DOCTYPE html>
<html lang="KO">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../assets/css/quizHome.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div id="wrap">
        <?php include "../include/header.php" ?>

        <div id="main">
            <div id="aside">
                <?php include "quizCategory.php" ?>
            </div>

            <section id="quiz_list">
                <div class="intro_inner">
                    <h3>카테고리</h3>
                    <article class="list">
                        <h3>풀고 싶은 문제만 모아서 보고싶다면 각 문제에 좋아요 표시를 하세요! 찜 목록에서 확인할 수 있습니다.</h3>
                        <button class="like_btn">찜 목록</button>
                    </article>
                </div>

                <div class="contents">
                    <div id="filter-options">
                        <button class="filter-button" data-filter="latest">최신순</button>
                        <button class="filter-button" data-filter="popular">인기순</button>
                        <button class="filter-button" data-filter="unsolved">안푼문제</button>
                    </div>
                    <div id="dynamicContent">
                        <section>
                            <?php foreach ($quizResult as $quiz) { ?>
                                <div class="card">
                                    <a href="quiz.php?quizId=<?= $quiz['quizId'] ?>">
                                        <?= $quiz['quizId'] ?>
                                        <ul class="card__text">
                                            <li>
                                                <?= $quiz['cate'] ?>
                                            </li>
                                            <div class="board_wrap">
                                                <div class="b_w_wrap">
                                                    <li>
                                                        <?= $quiz['cate'] ?>
                                                    </li>
                                                    <li class="card__desc">
                                                        <?= substr($quiz['question1'], 0, 100) ?>
                                                    </li>
                                                </div>
                                                <div class="b_w_wrap2">
                                                    <li>
                                                        <?= $quiz['likes'] ?>
                                                    </li>
                                                </div>
                                            </div>
                                        </ul>
                                    </a>
                                </div>
                            <?php } ?>
                        </section>
                    </div>
            </section>
        </div>
    </div>

    <?php include "../include/footer.php" ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <script>
        $(".filter-button").click(function () {
            var filter = $(this).data("filter");

            $("card").hide();

            if (filter === "latest") {
                $.ajax({
                    url: 'getLatestQuizzes.php',
                    type: 'GET',
                    success: function (data) {
                        $('#dynamicContent').html(data);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else if (filter === "popular") {
                $.ajax({
                    url: 'getPopularQuizzes.php',
                    type: 'GET',
                    success: function (data) {
                        $('#dynamicContent').html(data);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else if (filter === "unsolved") {
                // 안 푼 문제만 필터
                $.ajax({
                    url: 'getUnsolvedQuizzes.php',
                    type: 'GET',
                    success: function (data) {
                        $('#dynamicContent').html(data);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    </script>
    </div>
</body>

</html>