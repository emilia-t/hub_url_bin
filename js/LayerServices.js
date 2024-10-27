let LayerServices=document.getElementById('LayerServices');
let LayerServicesApp=document.getElementById('LayerServicesApp');
let LayerServicesList=document.getElementById('LayerServicesList');

let ComputerSvgA=()=>{
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    document.createElement("SVG");
    svg.setAttribute("class", "icon");
    svg.setAttribute("viewBox", "0 0 1024 1024");
    svg.setAttribute("version", "1.1");
    svg.setAttribute("width", "200");
    svg.setAttribute("height", "200");
    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", "M928 736V160H96v576h320v96h-64v64h320v-64h-64v-96z m-64-512v320H160V224zM160 672v-64h704v64z m384 160h-64v-96h64z");
    svg.appendChild(path);
    return svg;
};

let getNewItemService=(ip,ports)=>{
    let portsArr=ports.split(',');
    let len=portsArr.length;
    let domItem=document.createElement('DIV');
    let domBoxA=document.createElement('DIV');
    let svg=ComputerSvgA();
    let domBoxB=document.createElement('DIV');
    let domIp=document.createElement('DIV');
    let domPorts=document.createElement('DIV');
    domItem.setAttribute('class','LayerServicesItem');
    domBoxA.setAttribute('class','LayerServicesBoxA');
    domBoxB.setAttribute('class','LayerServicesBoxB');
    domIp.setAttribute('class','LayerServicesIp');
    domIp.innerText=ip;
    domPorts.setAttribute('class','LayerServicesPorts');
    for(let i=0;i<len;i++){
        let ipPort='';
        let port=portsArr[i];
        if(portsArr[i]==='443'){
            ipPort='https://'+ip;
        }else{
            ipPort='http://'+ip+':'+port;
        }
        let domA=document.createElement('A');
        domA.setAttribute('href','ipPort');
        domA.setAttribute('target','_blank');
        domA.innerText=port;
        domPorts.append(domA);
    }
    domBoxA.append(svg);
    domItem.append(domBoxA);

    domBoxB.append(domIp);
    domBoxB.append(domPorts);
    domItem.append(domBoxB);

    return domItem;
};

document.getElementById("LayerServicesBtn").addEventListener("click", function() {
    if(LayerServicesApp.classList.contains("visible")){
        LayerServicesApp.classList.remove("visible");
        this.style.transform='rotate(0deg)';
    }else{
        LayerServicesApp.classList.add("visible");
        this.style.transform='rotate(180deg)';
    }
});

subscribe(
    'ports',
    function(newValue){
        let len=newValue.length;
        LayerServicesList.innerHTML='';
        for(let i=0;i<len;i++){
            let item=getNewItemService(newValue[i].ip,newValue[i].ports);
            LayerServicesList.append(item);
        }
    }
);

subscribe(
  'config.theme',
  function(newValue){
      if(newValue==='dark'){
          LayerServices.classList.add('LayerServices-dark');
          LayerServices.classList.remove('LayerServices-white');
      }
      if(newValue==='white'){
          LayerServices.classList.add('LayerServices-white');
          LayerServices.classList.remove('LayerServices-dark');
      }
  }
);