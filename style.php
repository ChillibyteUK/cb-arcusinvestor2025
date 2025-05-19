<?php
/**
 * Template Name: Style Guide
 *
 * This template is used to display the CSS properties defined in the SASS file.
 *
 * @package cb-arcusinvestor2025
 */

if ( ! function_exists( 'wp_kses_post' ) ) {
	die( 'WordPress not loaded. Please access this page via the WordPress front end.' );
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= esc_url( get_stylesheet_directory_uri() . '/css/child-theme.css' ); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>">
    <style>
        h2 {
            border-bottom: 1px solid hsl(0 0% 0% / 0.2);
        }

        .data {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .output {
            display: grid;
            grid-template-columns: 200px auto;
        }

        .title {
            align-self: center;
        }

        .value {
            width: 100%;
            min-height: 50px;
            align-self: center;
        }

        .single {
            width: 100%;
            min-height: 50px;
        }

        .colour {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .tint {
            min-height: 25px;
        }
    </style>
</head>

<body>
    <div class="container-xl">
        <h1>CSS Properties</h1>
        <div class="data">
            <?php

            $file_path = get_stylesheet_directory() . '/src/sass/theme/_props.scss';
			if ( ! file_exists( $file_path ) ) {
				echo '<p>Could not find file: ' . esc_html( $file_path ) . '</p>';
				return;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$file_contents = file_get_contents( $file_path );
			if ( false === $file_contents ) {
				echo '<p>Failed to read file contents from: ' . esc_html( $file_path ) . '</p>';
				return;
			}

            preg_match_all( '/--(.*?)\s*:\s*(.*?)\s*;/m', $file_contents, $matches, PREG_SET_ORDER, 0 );

            $variables = array();
            $colours   = array();
            $fsizes    = array();
            $fweights  = array();

            foreach ( $matches as $match ) {
                $variable_name               = trim( $match[1] );
                $variable_value              = trim( $match[2] );
                $variables[ $variable_name ] = $variable_value;

				if ( preg_match( '/^col/', $variable_name ) ) {
					$colours[ $variable_name ] = $variable_value;
                }

                if ( preg_match( '/^fs/', $variable_name ) ) {
                    $fsizes[ $variable_name ] = $variable_value;
                }

                if ( preg_match( '/^fw/', $variable_name ) ) {
                    $fweights[ $variable_name ] = $variable_value;
                }
            }

            echo '<h2>Colours</h2>';
            foreach ( $colours as $name => $value ) {
                echo wp_kses_post( colour( $name, $value ) );
            }

            echo '<h2>Font Sizes</h2>';

            foreach ( array_reverse( $fsizes ) as $name => $value ) {
                echo wp_kses_post( type( $name, $value ) );
            }

            echo '<h2>Font Weights</h2>';
            foreach ( array_reverse( $fweights ) as $name => $value ) {
                echo wp_kses_post( weight( $name, $value ) );
            }

            /**
             * Generates HTML for a color variable display.
             *
             * @param string $name  The name of the CSS variable.
             * @param string $value The value of the CSS variable.
             * @return string The generated HTML for the color display.
             */
            function colour( $name, $value ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
                ob_start();
            	?>
                <div class="output">
                    <div class="title">--<?= esc_html( $name ); ?></div>
                    <div class="value">
                        <div class="single" style="background-color:var(--<?= esc_attr( $name ); ?>)"></div>
                    </div>
                </div>
            	<?php
                return ob_get_clean();
            }

            /**
             * Generates HTML for a font size variable display.
             *
             * @param string $name  The name of the CSS variable.
             * @param string $value The value of the CSS variable.
             * @return string The generated HTML for the font size display.
             */
            function type( $name, $value ) {
                ob_start();
            	?>
                <div class="output">
                    <div class="title">--<?= esc_html( $name ); ?></div>
                    <div class="value" style="font-size:<?= esc_attr( $value ); ?>">Lorem ipsum dolor sit amet consectetur.</div>
                </div>
            	<?php
                return ob_get_clean();
            }

            /**
             * Generates HTML for a font weight variable display.
             *
             * @param string $name  The name of the CSS variable.
             * @param string $value The value of the CSS variable.
             * @return string The generated HTML for the font weight display.
             */
            function weight( $name, $value ) {
                ob_start();
            	?>
                <div class="output">
                    <div class="title">--<?= esc_html( $name ); ?></div>
                    <div class="value" style="font-size:var(--fs-400);font-weight:<?= esc_attr( $value ); ?>">Lorem ipsum dolor sit amet consectetur.</div>
                </div>
            	<?php
                return ob_get_clean();
            }

            ?>
        </div>
		<hr>
    </div>
</body>

</html>