document.body.addEventListener("pointermove",(e)=>{
    const {currentTarget: el,clientX: x,clientY: y} = e;
    const{top:t,left:l,width:w,height:h} = e1.getBoundingclientRect();
    e1.style.setProperty("--posX", x-1-w/2);
    e1.style.setProperty("--posY", y-t-h/2);
})

const elts = {

    text1:document.getElementById("text1"),
    text2:document.getElementById("text2")

};

const texts = [
    

    
];


const morphTime = 1;
const cooldownTime = 0.25;

let textIndex = texts.length-1;
let time = new Date();
let morph = 0;
let cooldown = cooldownTime;


elts.text1.textContent = texts[textIndex % texts.length];
elts.text2.textContent = texts[(textIndex + 1) % texts.length];

function doMorph()
{

    morph-= cooldown;
    cooldown = 0;

    let fraction = morph / morphTime;

    if(fraction > 1){
        cooldown = cooldownTime;
        fraction = 1;
    }

    setMorph(fraction);


}

function setMorph(fraction){
    elts.text2.style.filter = `blur(${Math.min(8 / fraction - 8,100)}px)`;

    elts.text2.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`;


    fraction = 1 - fraction;

    elts.text1.textContent = texts[textIndex % texts.length];

    elts.text2.textContent = texts[(textIndex + 1) % texts.length];


}


function doCooldown(){
    morph = 0;
    
    elts.text2.style.filter = "";
    elts.text2.style.opacity = "100%";

    elts.text1.style.filter = "";
    elts.text1.style.opacity = "0%";

}

function animate()
{
    requestAnimationFrame(animate);

    let newTime = new Date();
    let shouldIncrementIndex = cooldown > 0;
    let dt = (newTime - time) / 1000;
    time = newTime;
    cooldown -= dt;

    if(cooldown <= 0){
        if(shouldIncrementIndex){
            textIndex++;
        }

        doMorph();

    }
    else{}

}



//  hamburger = document.querySelector(".hamburger");
//         hamburger.onclick = function(){
//             navBar = document.querySelector(".nav-bar");
//             navBar.classList.toggle("active");
//         }

//         const cursor = document.querySelector('.cursor');
//         document.addEventListener('mousemove',(e) => {
//             cursor.style.left = e.pageX + 'px';
//             cursor.style.top = e.pageY + 'px';
//         })
    
