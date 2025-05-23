<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mosaico Estudantil</title>
    <style>
        body {
            margin: 0;
            background-color: #000;
            overflow: hidden;
        }
        #mosaic-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }
        .photo {
            position: absolute;
            object-fit: cover;
            border-radius: 5px;
            border: 7px solid white; /* Borda branca */
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5); /* Sombra para efeito de sobreposição */
            transition: all 0.5s ease;
            z-index: 1; /* Garante camadas visíveis */
        }
        @media (max-width: 768px) {
            .photo { width: 100px !important; height: 100px !important; }
        }
        @media (min-width: 769px) {
            .photo { width: 150px !important; height: 150px !important; }
        }
    </style>

    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
    <div id="mosaic-container"></div>

    <?php
    $diretoria1 = "aluno/fotos/";
    $diretoria2 = "aluno/fotos2/";
    $diretoria3 = "professor/fotos/";
    $imagens1 = glob($diretoria1 . "*.{jpg,JPG,jpeg,JPEG}", GLOB_BRACE);
    $imagens2 = glob($diretoria2 . "*.{jpg,JPG,jpeg,JPEG}", GLOB_BRACE);
    $imagens3 = glob($diretoria3 . "*.{jpg,JPG,jpeg,JPEG}", GLOB_BRACE);
    $imagens = array_merge((array)$imagens1, (array)$imagens2, (array)$imagens3);
    shuffle($imagens);

    $basePath = dirname($_SERVER['PHP_SELF']) === '/' ? '' : dirname($_SERVER['PHP_SELF']);
    $imagens = array_map(function($path) use ($basePath) {
        return $basePath . '/' . $path;
    }, $imagens);
    ?>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            const container = $('#mosaic-container');
            const photoPool = <?php echo json_encode($imagens); ?>;
            const photoHeight = window.innerWidth <= 768 ? 100 : 150; // Altura/largura da foto
            const maxPhotos = 200; // Limite de 200 fotos

            console.log('Total de imagens encontradas:', photoPool.length);
            console.log('Exemplo de imagens:', photoPool.slice(0, 5));

            if(photoPool.length === 0) {
                console.error('Nenhuma foto encontrada');
                container.append('<p style="color: white; text-align: center;">Nenhuma imagem encontrada</p>');
                return;
            }

            let photoCount = 0; // Contador de fotos na tela

            function getRandomPosition() {
                const containerWidth = container.width();
                const containerHeight = container.height();
                return {
                    left: Math.random() * (containerWidth - photoHeight),
                    top: Math.random() * (containerHeight - photoHeight)
                };
            }

            function getRandomRotation() {
                return Math.random() * 30 - 15;
            }

            function getRandomAnimation() {
                const animations = [
                    {opacity: 0, rotate: -90}, // Fade com rotação
                    {top: -100, opacity: 0},   // Slide de cima
                    {scale: 0, opacity: 0}     // Zoom
                ];
                return animations[Math.floor(Math.random() * animations.length)];
            }

            function createPhoto() {
                if(photoPool.length === 0) return;

                // Verifica o limite de 500 fotos
                if(photoCount >= maxPhotos) {
                    console.log('Limite de 500 fotos atingido, reiniciando...');
                    container.empty(); // Remove todas as fotos
                    photoCount = 0; // Reinicia o contador
                }

                const photoIndex = Math.floor(Math.random() * photoPool.length);
                const photoSrc = photoPool[photoIndex];
                console.log('Criando foto:', photoSrc);

                const $photo = $('<img>', {
                    class: 'photo',
                    src: photoSrc
                }).on('error', function() {
                    console.error('Erro ao carregar:', photoSrc);
                    $(this).remove();
                    createPhoto(); // Tenta outra foto se falhar
                });

                const position = getRandomPosition();
                const rotation = getRandomRotation();
                const animation = getRandomAnimation();

                $photo.css({
                    left: position.left,
                    top: position.top,
                    opacity: 0,
                    transform: `rotate(${rotation}deg)`,
                    zIndex: photoCount // Aumenta o z-index para novas fotos ficarem por cima
                });

                container.append($photo);
                photoCount++;

                // Animação de entrada
                $photo.animate({
                    opacity: 1,
                    top: position.top,
                    scale: 1
                }, 500);
            }

            function initializeMosaic() {
                console.log('Inicializando mosaico...');
                setInterval(createPhoto, 1000); // Nova foto a cada 1s
            }

            $(window).resize(function() {
                const newPhotoHeight = window.innerWidth <= 768 ? 100 : 150;
                $('.photo').each(function() {
                    const position = getRandomPosition();
                    $(this).css({
                        left: position.left,
                        top: position.top
                    });
                });
            });

            initializeMosaic();
        });
    </script>
</body>
</html>