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

    // add event element

    const addEventOnelem = function (elem, type, callback) {
        if (elem.length > 1) {
            for (let i = 0; i < elem.length; i++) {
                elem[i].addEventListener(type, callback);
            }
        } else {
            elem.addEventListener(type, callback);
        }
    }

    // toggle chapter

    const chapterToggler = document.querySelector("[data-chapter-toggler]");

    const toggleChapter = function () {
        chapterToggler.classList.toggle("active");
    }

    addEventOnelem(chapterToggler, 'click', toggleChapter);
});