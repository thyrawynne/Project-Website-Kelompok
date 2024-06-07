document.addEventListener("DOMContentLoaded", function() {
  'use strict';

  // add event element

  const addEventOnelem = function (elem, type, callback) {
      if (elem.length > 1) {
        for (let i = 0; i < elem.length; i++) {
          elem[i].addEventListener(type, callback);
        }
      } 
      else {
        elem.addEventListener(type, callback);
      }
  }

  // toggle menu

  const navbar = document.querySelector("[data-navbar]");
  const navbarLinks = document.querySelectorAll("[data-nav-link]");
  const navToggler = document.querySelector("[data-nav-toggler]");

  const toggleNavbar = function () {
    navbar.classList.toggle("active");
    navToggler.classList.toggle("active");
  }

  addEventOnelem(navToggler, 'click', toggleNavbar);

  const closeNavbar = function () {
    navbar.classList.remove("active");
    navToggler.classList.remove("active");
  }

  addEventOnelem(navbarLinks, "click", closeNavbar);


  // header pc 

  const header = document.querySelector("[data-header]");

  const activeHeader = function () {
    if (window.scrollY > 100) {
      header.classList.add("active");
    } else {
      header.classList.remove("active");
    }
  }

  addEventOnelem(window, "scroll", activeHeader);

  // tabcard

  const tabCard = document.querySelectorAll("[data-tab-card]");

  let lastTabCard = tabCard[0];

  const navigateTab = function () {
      lastTabCard.classList.remove("active");
      this.classList.add("active");
      lastTabCard = this;
  }

  tabCard.forEach(card => {
      card.addEventListener("click", navigateTab);

      const iframe = card.querySelector('iframe');
      const spinner = card.querySelector('.loading-spinner');

      iframe.addEventListener('load', () => {
          spinner.style.display = 'none';
      });

      iframe.addEventListener('error', () => {
          spinner.style.display = 'none';
          // handle error
      });
  });


  addEventOnelem(tabCard, "click", navigateTab);

  // scrollToTopBtn

  // Get the button
  let mybutton = document.getElementById("scrollToTopBtn");

  // When the user scrolls down 100px from the top of the document, show the button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
      if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
          mybutton.style.display = "block";
      } else {
          mybutton.style.display = "none";
      }
  }

  // When the user clicks on the button, scroll to the top of the document
  mybutton.onclick = function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
