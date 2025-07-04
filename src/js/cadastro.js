document.addEventListener('DOMContentLoaded', () =>{

const progress = document.querySelector('.progress');
const stepIndicators = document.querySelectorAll('.step-indicator li');

document.documentElement.style.setProperty('--steps', stepIndicators.length);

let currentStep = 0;

setInterval(() => {

    currentStep++;

    if(currentStep > stepIndicators.length - 1) {
        currentStep = 0;
    }

    let width = currentStep / (stepIndicator.length - 1);
    progress.style.transform = `scaleX(${width})`;

    stepIndicators.forEach((indicator, index) => {
        indicator.classList.toggle("current", currentStep === index)
        indicator.classList.toggle("done", currentStep > index)

    })

    },2000);
});
