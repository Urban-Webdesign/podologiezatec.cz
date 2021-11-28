import "@theme/front/init.scss";

import "bootstrap/js/dist/dropdown";
import "bootstrap/js/dist/collapse";
import "bootstrap/js/dist/modal";
import "bootstrap/js/dist/util";

import "lightbox2/dist/css/lightbox.css";
// eslint-disable-next-line no-unused-vars
import lightbox from "lightbox2/dist/js/lightbox";

import Nette from "@/front/netteForms";
Nette.initOnLoad();
window.Nette = Nette;


const $scrollTopBtn = document.querySelector("#scrollTopBtn");
const navbar = document.querySelector(".navbar");

function runOnScroll() {
	var currentScrollPos = window.pageYOffset;


	if (window.innerWidth >= 992) {
		if (window.innerWidth > 1199) {
			if (currentScrollPos >= 100) {
				navbar.style.marginTop = 0;
				navbar.style.position = "fixed";
			} else {
				navbar.style.marginTop = "100px";
				navbar.style.position = "absolute";
			}
		} else if (currentScrollPos >= 70) {
			navbar.style.marginTop = 0;
			navbar.style.position = "fixed";
		} else {
			navbar.style.marginTop = "70px";
			navbar.style.position = "absolute";
		}
	}

	if (window.innerWidth < 576) {
		if (currentScrollPos > window.innerHeight) {
			$scrollTopBtn.style.display = "flex";
		} else {
			$scrollTopBtn.style.display = "none";
		}
	} else {
		if (currentScrollPos > window.innerHeight) {
			$scrollTopBtn.style.right = "0";
		} else {
			$scrollTopBtn.style.right = "-170px";
		}
	}

}

document.addEventListener("DOMContentLoaded", () => {
	// modal after registration
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	var odeslano = urlParams.get("odeslano");
	if(odeslano){
		$("#mailSentModal").modal("show");
		console.log("modal is shown");
	}

	// on scroll events
	// eslint-disable-next-line no-undef
	runOnScroll();
	// eslint-disable-next-line no-undef
	window.addEventListener("scroll", runOnScroll);


	// smooth scroll
	// Add smooth scrolling to all links
	$(".scroll").on("click", function(event) {

		// Make sure this.hash has a value before overriding default behavior
		if (this.hash !== "") {
			// Prevent default anchor click behavior
			event.preventDefault();

			// Store hash
			var hash = this.hash;

			// Using jQuery's animate() method to add smooth page scroll
			// The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
			$("html, body").animate({
				scrollTop: $(hash).offset().top
			}, 600, function(){

				// Add hash (#) to URL when done scrolling (default click behavior)
				window.location.hash = hash;
			});
		} // End if
	});

	// close navbar on link click
	$("#navbar .scroll").click(function(){
		$(".navbar-collapse").collapse("hide");
	});
});
