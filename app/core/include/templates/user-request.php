<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">

        <title>Заявка #<?php echo $request; ?></title>

        <style>
            body {
                display: block;

                margin: 0;
                padding: 10px;

                font: normal 14px/1.4 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;

                background-color: #eee;
            }

            section {
                display: block;
                box-sizing: border-box;

                width: 100%;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;

                background-color: #fff;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);

                border: 1px solid #e5e5e5;
            }

            header {
                display: grid;
                grid-template-columns: auto 1fr;
                grid-column-gap: 20px;
                grid-row-gap: 7px;

                margin: 10px 0 20px;
                padding: 20px 0;

                border: solid 1px #eee;
                border-left: 0;
                border-right: 0;
            }

            h1 {
                display: block;

                margin: 0;

                font-size: 23px;
                font-weight: 400;
            }
        </style>
    </head>

    <body>
        <section>
            <h1>Заявка #<?php echo $request; ?></h1>

            <header>
                <?php
                    printf(
                        __('<strong>Имя:</strong><span>%s</span>', 'knife-theme'),
                        sanitize_text_field($name)
                    );
                    printf(
                        __('<strong>E-mail:</strong><span>%s</span>', 'knife-theme'),
                        sanitize_text_field($email)
                    );
                    printf(
                        __('<strong>Тема:</strong><span>%s</span>', 'knife-theme'),
                        sanitize_text_field($subject)
                    );
                ?>
            </header>

            <article>
                <?php echo nl2br(esc_html($text)); ?>
            </article>
        </section>
    </body>
</html>
