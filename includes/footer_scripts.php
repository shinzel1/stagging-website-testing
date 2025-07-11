<script src="assets/js/swiper-bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySwiper = new Swiper('.category-carousel', {
            slidesPerView: 5,
            spaceBetween: 20,
            navigation: {
                nextEl: '.category-carousel-next',
                prevEl: '.category-carousel-prev',
            },
            loop: true,
            breakpoints: {
                320: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 2, spaceBetween: 15 },
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#newsletterForm').on('submit', function (e) {
            e.preventDefault();
            const email = $('#newsletterEmail').val().trim();

            if (!email) return;

            $.ajax({
                url: 'includes/subscribe_newsletter.php',
                method: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function (response) {
                    const messageBox = $('#newsletterMessage');
                    if (response.success) {
                        messageBox.text(response.message).css('color', 'green');
                        $('#newsletterEmail').val('');
                    } else {
                        messageBox.text(response.message).css('color', 'red');
                    }
                },
                error: function () {
                    $('#newsletterMessage').text("Something went wrong. Please try again.")
                        .css('color', 'red');
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/script.js"></script>