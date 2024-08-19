'use strict';

const addEventOnElem = function (elem, type, callback) {
  if (elem.length > 1) {
    for (let i = 0; i < elem.length; i++) {
      elem[i].addEventListener(type, callback);
    }
  } else {
    elem.addEventListener(type, callback);
  }
};

const slider = document.querySelector("[data-slider]");
const nextBtn = document.querySelector("[data-next]");
const prevBtn = document.querySelector("[data-prev]");

let sliderPos = 0;
const totalSliderItems = 4;

const slideToNext = function () {
  sliderPos++;
  slider.style.transform = `translateX(-${sliderPos}00%)`;
  sliderEnd();
};

addEventOnElem(nextBtn, "click", slideToNext);

const slideToPrev = function () {
  sliderPos--;
  slider.style.transform = `translateX(-${sliderPos}00%)`;
  sliderEnd();
};

addEventOnElem(prevBtn, "click", slideToPrev);

function sliderEnd() {
  if (sliderPos >= totalSliderItems - 1) {
    nextBtn.classList.add("disabled");
  } else {
    nextBtn.classList.remove("disabled");
  }

  if (sliderPos <= 0) {
    prevBtn.classList.add("disabled");
  } else {
    prevBtn.classList.remove("disabled");
  }
}

sliderEnd();
