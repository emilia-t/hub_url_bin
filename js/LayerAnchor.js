let LayerAnchor=document.getElementById("LayerAnchor");
LayerAnchor.addEventListener("click",()=>{
    console.log("power by hub_url_bin");
});

subscribe(
     'config.theme',
    function(newValue){
        if(newValue==='dark'){
            LayerAnchor.classList.add('LayerAnchor-dark');
            LayerAnchor.classList.remove('LayerAnchor-white');
        }
        if(newValue==='white'){
            LayerAnchor.classList.add('LayerAnchor-white');
            LayerAnchor.classList.remove('LayerAnchor-dark');
        }
    }
);