let LayerAnchor=document.getElementById("LayerAnchor");
LayerAnchor.addEventListener("click",()=>{
    console.log("Power by hub_url_bin (C) Minxi Wan");
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

subscribe(
    'onlineNumber',
    function(newValue){
        if(typeof newValue==='number'){
            LayerAnchor.innerHTML='当前连接数：'+newValue+'&nbsp;&nbsp;';
        }
    }
);