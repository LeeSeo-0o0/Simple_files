<!DOCTYPE html>  
<html lang="ko">  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>파일 및 폴더 목록</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container {
            margin-top: 50px;
            border-radius: 12px;
            background-color: #007bff;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .list-group-item {
            border: none;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #ffffff;
            color: #333;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .list-group-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background-color: #f1f1f1;
        }
        .list-group-item i {
            margin-right: 10px;
        }
        .list-group-item a {
            color: #333;
            text-decoration: none;
            padding-left: 10px; /* 시각적으로만 공백 추가 */
        }
        .list-group-item a:hover {
            text-decoration: underline;
        }
        h1 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            margin-bottom: 20px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .dropdown-menu {
            min-width: 150px;
        }
    </style>  
</head>  
<body>  
<div class="container">
    <h1 class="mt-4">파일 및 폴더 목록</h1>
    <?php
    $baseDir = realpath('.'); // 기본 디렉토리의 실제 경로
    $currentDir = isset($_GET['dir']) ? realpath($baseDir . '/' . $_GET['dir']) : $baseDir;

    // 기본 디렉토리 밖으로 나가지 않도록 제한
    if ($currentDir === false || strpos($currentDir, $baseDir) !== 0) {
        $currentDir = $baseDir;
    }

    // 상위 폴더로 이동 링크
    if ($currentDir !== $baseDir) {
        $parentDir = dirname($currentDir);
        echo '<a href="?dir=' . urlencode($parentDir) . '" class="btn btn-secondary"><i class="fas fa-arrow-up"></i> 상위 폴더로 이동</a>';
    }

    echo '<ul class="list-group">';

    $scanned_directory = array_diff(scandir($currentDir), array('..', '.'));

    // index.php와 index.html 파일을 숨기기 위한 필터링
    $scanned_directory = array_filter($scanned_directory, function($item) use ($currentDir) {
        return !in_array($item, array('index.php', 'index.html'));
    });

    foreach ($scanned_directory as $item) {
        $path = $currentDir . '/' . $item;
        $encodedPath = urlencode($path);
        $safeItem = htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); // XSS 방지용

        if (is_dir($path)) {
            echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-folder"></i><a href="?dir=' . urlencode($path) . '">' . $safeItem . '</a>
                    </div>
                  </li>';
        } else {
            echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file"></i><a href="' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '" download>' . $safeItem . '</a>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle text-dark" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><button class="dropdown-item" onclick="copyDownloadLink(\'' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '\')">다운로드 링크 복사</button></li>
                        </ul>
                    </div>
                  </li>';
        }
    }

    echo '</ul>';
    ?>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    function copyDownloadLink(link) {
        const url = window.location.origin + '/' + encodeURI(link);
        navigator.clipboard.writeText(url).then(function() {
            alert('다운로드 링크가 복사되었습니다: ' + url);
        }, function(err) {
            alert('링크 복사에 실패했습니다.');
        });
    }
</script>
</body>  
</html>
