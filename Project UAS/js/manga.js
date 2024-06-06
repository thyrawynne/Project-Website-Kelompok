document.addEventListener('DOMContentLoaded', function(){
    const mybutton = document.getElementById("scrollToTopBtn");

    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    mybutton.addEventListener('click', function() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    });

    // READ MORE DESC
    const readMoreBtn = document.querySelector(".read-more-btn");
    const descriptionText = document.querySelector(".description-text");

    readMoreBtn.addEventListener("click", function () {
        descriptionText.classList.toggle("expanded");
        if (descriptionText.classList.contains("expanded")) {
            readMoreBtn.textContent = "Read Less...";
        } else {
            readMoreBtn.textContent = "Read More...";
        }
    });

});