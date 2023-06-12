function accionPlay() {
    medio.volume = 0.5
    if (!medio.paused && !medio.ended) {
        medio.pause();
        
        play.value = '\u25BA'; //\u25BA
        document.body.style.backgroundColor = '#fff';
    }
    else {
        medio.play();
        play.value = '||';
         
        document.body.style.backgroundColor = 'grey';
    }
}
function accionReiniciar() {
    medio.currentTime = 0;
    medio.play();
    play.value = '||'
}
function accionRetrasar() {
    if (medio.currentTime > 0) {
        medio.currentTime = medio.currentTime - 3;
        medio.play();
    }

}
function accionAdelantar() {
    if (medio.currentTime < medio.duration) {
        medio.currentTime = medio.currentTime + 3;
        medio.play();
    }

}
function accionSilenciar() {
    if (medio.muted === true) {
        medio.muted = false
        silenciar.value = "mutear"
    } else if (medio.muted === false) {
        medio.muted = true;
        silenciar.value = "escuchar"
    }
}
function accionMasVolumen() {
    if(medio.volume <= 1 )
    medio.volume= medio.volume+0.1
    console.log(medio.volume)
}
function accionMenosVolumen() {
    if(medio.volume >= 0 )
    medio.volume= medio.volume-0.1
    //Contemplar que el valor mínimo de volumen es 0
    console.log(medio.volume)
}

function iniciar() {

    medio = document.getElementById('medio');
    play = document.getElementById('play');
    reiniciar = document.getElementById('reiniciar');
    retrasar = document.getElementById('retrasar');
    adelantar = document.getElementById('adelantar');
    silenciar = document.getElementById('silenciar');

    play.addEventListener('click', accionPlay);
    reiniciar.addEventListener('click', accionReiniciar);
    retrasar.addEventListener('click', accionRetrasar);
    adelantar.addEventListener('click', accionAdelantar);
    silenciar.addEventListener('click', accionSilenciar);
    menosVolumen.addEventListener('click', accionMenosVolumen);
    masVolumen.addEventListener('click', accionMasVolumen);
}

window.addEventListener('load', iniciar, false);