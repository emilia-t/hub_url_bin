let LayerContent=document.getElementById('LayerContent');

let getNewItemApp=(url,icon,title)=>{
    let domDiv=document.createElement('SPAN');
    let domA=document.createElement('A');
    let domImg=document.createElement('IMG');
    let domSpan=document.createElement('span');
    domDiv.setAttribute('class','LayerContentAppItemV');
    domA.setAttribute('href',url);
    domA.setAttribute('class','LayerContentAppItem');
    domA.setAttribute('target','_blank');
    domImg.setAttribute('src',icon);
    domImg.setAttribute('alt','');
    domSpan.setAttribute('class','LayerContentAppTitle');
    domSpan.innerText=title;
    domA.appendChild(domImg);
    domA.appendChild(domSpan);
    domDiv.appendChild(domA);
    return domDiv;
};

subscribe(
    'config.theme',
    function(newValue){
        setTimeout(
            ()=>{
                let LayerContentAppItem=document.getElementsByClassName('LayerContentAppItem');
                let Length=LayerContentAppItem.length;
                if(newValue==='dark'){
                    document.body.style.backgroundColor='#333333';
                    document.body.style.color='#ffffff';
                    for(let i=0;i<Length;i++){
                        LayerContentAppItem[i].classList.add('LayerContentAppItem-dark');
                        LayerContentAppItem[i].classList.remove('LayerContentAppItem-white');
                    }
                }
                if(newValue==='white'){
                    document.body.style.backgroundColor='#ffffff';
                    document.body.style.color='#000000';
                    for(let i=0;i<Length;i++){
                        LayerContentAppItem[i].classList.add('LayerContentAppItem-white');
                        LayerContentAppItem[i].classList.remove('LayerContentAppItem-dark');
                    }
                }
            }
        ,10);
    }
);

subscribe(
  'webs',
  function(newValue){
    let len=newValue.length;
    LayerContent.innerHTML='';
    for(let i=0;i<len;i++){
        let item=getNewItemApp(newValue[i].url,newValue[i].icon,newValue[i].title);
        LayerContent.append(item);
    }
    //更新了dom后 class 也要重置
    let LayerContentAppItem=document.getElementsByClassName('LayerContentAppItem');
    let Length=LayerContentAppItem.length;
    if($store['config.theme']==='dark'){
        document.body.style.backgroundColor='#333333';
        document.body.style.color='#ffffff';
        for(let i=0;i<Length;i++){
            LayerContentAppItem[i].classList.add('LayerContentAppItem-dark');
            LayerContentAppItem[i].classList.remove('LayerContentAppItem-white');
        }
    }
    if($store['config.theme']==='white'){
        document.body.style.backgroundColor='#ffffff';
        document.body.style.color='#000000';
        for(let i=0;i<Length;i++){
            LayerContentAppItem[i].classList.add('LayerContentAppItem-white');
            LayerContentAppItem[i].classList.remove('LayerContentAppItem-dark');
        }
    }
  }
);