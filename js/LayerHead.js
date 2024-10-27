let LayerHead=document.getElementById("LayerHead");
let LayerHeadTheme=document.getElementById("LayerHeadTheme");
let LayerHeadA=document.getElementsByClassName('LayerHeadA');

let startSettingHead=()=>{
    for(let i=0;i<LayerHeadA.length;i++){
        LayerHeadA[i].addEventListener('mouseenter',(e)=>{
            let theme=$store['config.theme'];
            if(theme==='white'){
                LayerHeadA[i].classList.add('LayerHeadA-hover-white');
            }
            if(theme==='dark'){
                LayerHeadA[i].classList.add('LayerHeadA-hover-dark');
            }
        });
        LayerHeadA[i].addEventListener('mouseleave',(e)=>{
            LayerHeadA[i].classList.remove('LayerHeadA-hover-white');
            LayerHeadA[i].classList.remove('LayerHeadA-hover-dark');
        });
    }
    LayerHeadTheme.addEventListener('click',(e)=>{
        if($store['config.theme']==='dark'){
            Instruct.broadcastThemeChange('white');
        }else{
            Instruct.broadcastThemeChange('dark');
        }
    });
};

startSettingHead();

subscribe(
     'config.theme',
    function(newValue){
        if(newValue==='dark'){
            LayerHead.classList.add('LayerHead-dark');
            LayerHead.classList.remove('LayerHead-white');
            for(let i=0;i<LayerHeadA.length;i++){
                LayerHeadA[i].classList.add('LayerHeadA-dark');
                LayerHeadA[i].classList.remove('LayerHeadA-white');
            }
            LayerHeadTheme.innerText='dark';
        }
        if(newValue==='white'){
            LayerHead.classList.add('LayerHead-white');
            LayerHead.classList.remove('LayerHead-dark');
            for(let i=0;i<LayerHeadA.length;i++){
                LayerHeadA[i].classList.add('LayerHeadA-white');
                LayerHeadA[i].classList.remove('LayerHeadA-dark');
            }
            LayerHeadTheme.innerText='white';
        }
    }
);