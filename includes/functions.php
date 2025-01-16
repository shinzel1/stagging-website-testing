<?php
        function formatPrice($price) {
            return "$" . number_format($price, 2);
        }

        function redirect($url) {
            header("Location: $url");
            exit;
        }
        ?>