//PCM（Pulse Code Modulation，脉冲编码调制）
//是一种音频数据的编码格式，用于将模拟信号（如声音）数字化。
// 它是音频处理中最基本的格式，很多高级格式（如MP3、AAC等）都是基于PCM数据进行压缩的。
// PCM数据本质上就是对音频波形的采样数据点序列，因此包含了每个采样点的振幅信息。
let voiceSocket=null;// 通信器
let sendInterval =500;// 每隔0.5秒发送音频数据
let layerVoiceContainer =document.getElementById('LayerVoiceContainer');
let LayerVoiceAudioA=document.getElementById('LayerVoiceAudioA');
let LayerVoiceOpen=document.getElementById('LayerVoiceOpen');
let LayerVoiceClose=document.getElementById('LayerVoiceClose');
var Recorder=class Recorder{
    constructor(channelCount,inputChannels,outputChannels,sampleBits,sampleRate,bufferSize){
        this.isGetStream=false;//是否获取到了麦克风
        this.channelCount=typeof channelCount==='number'?channelCount:1;//声道总数
        this.inputChannels=typeof inputChannels==='number'?inputChannels:1;//(输入)声道数
        this.outputChannels=typeof outputChannels==='number'?outputChannels:1;//(输出)声道数
        this.inputSampleBits=typeof sampleBits==='number'?sampleBits:16;//(输入)采样数位，越高音质越好
        this.outputSampleBits=typeof sampleBits==='number'?sampleBits:16;//(输出)采样数位，越高音质越好
        this.inputSampleRate=typeof sampleRate==='number'?sampleRate:8000;//(输入)采样率，越高文件越大
        this.outputSampleRate=typeof sampleRate==='number'?sampleRate:8000;//(输出)采样率，越高文件越大
        this.bufferSize=typeof bufferSize==='number'?bufferSize:1024;//单次音频缓存大小(单位bty)，越高文件越大

        this.audioInput=null;
        this.audioContext=null;
        this.stream=null;
        this.volume=null;
        this.recorder=null;

        this.cacheSize=0;//缓存长度
        this.caches=[];//音频缓存

        this.startSetting().then(r=>void 0);
    }
    // 释放麦克风资源
    releaseMicrophone() {
        if (this.stream) {
            // 停止所有音轨，完全释放麦克风资源
            this.stream.getTracks().forEach(track => track.stop());
            this.isGetStream = false;
            console.log("麦克风已释放");
        }
    }
    /**
     * 获取一个麦克风音频流，如果获取失败则返回false
     * @returns {Promise<MediaStream|boolean>}
     */
    async getMicrophoneStream(){
        try{
            const stream=await navigator.mediaDevices.getUserMedia({audio:true});
            this.isGetStream=true;
            return stream;
        }catch(error){
            this.isGetStream=false;
            console.error("获取麦克风失败：",error);
            return false;
        }
    }
    async startSetting(){
        this.stream=await this.getMicrophoneStream();
        if(this.stream!==false){
            this.audioContext=new (window.AudioContext || window.webkitAudioContext)();
            this.audioInput=this.audioContext.createMediaStreamSource(this.stream);
            this.volume=this.audioContext.createGain();
            this.audioInput.connect(this.volume);
            this.recorder=this.audioContext.createScriptProcessor(this.bufferSize,this.channelCount,this.channelCount);//缓存，声道数，声道数

            this.inputSampleRate=this.audioContext.sampleRate===undefined?this.outputSampleRate:this.audioContext.sampleRate;
            this.inputSampleBits=this.audioContext.sampleBits===undefined?this.outputSampleBits:this.audioContext.sampleBits;

            this.recorder.onaudioprocess=(e)=>{
                this.inputSource(e.inputBuffer.getChannelData(0))
            };
            return true;
        }
        else{
            return false;
        }
    }
    /**
     *  存入音频原始缓存 来自 -> onaudioprocess.e.inputBuffer
     * @param data
     * @return boolean
     */
    inputSource(data){
        this.caches.push(new Float32Array(data));//将二进制数据转化为固定长度的32位浮点数
        this.cacheSize+=data.length;
        return true;
    }
    /**
     * 获取合并后的原始源音频数据
     * @returns {Float32Array}
     */
    mergeRawData(){
        let rawData=new Float32Array(this.cacheSize);//根据已存入的缓存大小创建相同的32位浮点固定数组
        let whiteEndNum=0;//最后一次写入的末尾位置
        let cacheLength=this.caches.length;
        for(let i=0;i<cacheLength;i++){//循环遍历将所有缓存写入到一个变量内(data)
            rawData.set(this.caches[i],whiteEndNum);
            whiteEndNum+=this.caches[i].length;
        }
        return rawData;
    }
    /**
     * 压缩原始音频数据
     * @returns {Float32Array}
     * @param rawData
     */
    compressRawData(rawData){
        let Number=Math.floor(this.inputSampleRate/this.outputSampleRate);
        let length=rawData.length/Number;
        let result=new Float32Array(length);
        let index1=0;
        let index2=0;
        while (index1<length){
            result[index1]=rawData[index2];
            index2+=Number;
            index1++;
        }
        return result;
    }
    getWavData(){
        let sampleRate=Math.min(this.inputSampleRate, this.outputSampleRate);
        let sampleBits=Math.min(this.inputSampleBits, this.outputSampleBits);
        let rawData=this.mergeRawData();
        rawData=this.compressRawData(rawData);
        let maxLength=rawData.length*(sampleBits/8);
        let buffer=new ArrayBuffer(44+maxLength);
        let data=new DataView(buffer);
        let offset=0;
        let writeString=(str)=>{
            for(let i=0;i<str.length;i++){
                data.setUint8(offset+i,str.charCodeAt(i));
            }
        };
        writeString('RIFF');
        offset+=4;
        data.setUint32(offset, 36+maxLength, true);
        offset+=4;
        writeString('WAVE');
        offset+=4;
        writeString('fmt ');
        offset+=4;
        data.setUint32(offset, 16, true);
        offset+=4;
        data.setUint16(offset, 1, true);
        offset+=2;
        data.setUint16(offset, this.channelCount, true);
        offset+=2;
        data.setUint32(offset, sampleRate, true);
        offset+=4;
        data.setUint32(offset, this.channelCount * sampleRate * (sampleBits / 8), true);
        offset+=4;
        data.setUint16(offset, this.channelCount * (sampleBits / 8), true);
        offset+=2;
        data.setUint16(offset, sampleBits, true);
        offset+=2;
        writeString('data');
        offset+=4;
        data.setUint32(offset, maxLength, true);
        offset+=4;
        data=this.reshapeWavData(sampleBits, offset, rawData, data);
        return data;
    }
    // 8位采样数位
    reshapeWavData(sampleBits,offset,iBytes,oData){
        if(sampleBits===8){
            for (let i=0;i<iBytes.length;i++,offset++){
                let s=Math.max(-1,Math.min(1,iBytes[i]));
                let val=s<0?s*0x8000:s*0x7FFF;
                val=Math.floor(255/(65535/(val+32768)));
                oData.setInt8(offset,val,true);
            }
        }else{
            for(let i=0;i<iBytes.length;i++,offset+=2){
                let s=Math.max(-1,Math.min(1,iBytes[i]));
                oData.setInt16(offset,s<0?s*0x8000:s*0x7FFF,true);
            }
        }
        return oData;
    }
    getWavFile(){
        let data=this.getWavData();
        return new Blob([data],{type:'audio/wav'});
    }
    closeContext(){
        this.audioContext.close();
    }
    // 开始
    start(){
        this.caches.length=0;//清空缓存
        this.cacheSize=0;
        this.audioInput.connect(this.recorder);
        this.recorder.connect(this.audioContext.destination)
    }
    // 获取音频文件
    getBlob(){
        this.stop();
        return this.getWavFile()
    }
    // 停止
    stop(){
        this.recorder.disconnect();
    }
    // 关闭
    close(){
        this.closeContext()
    }
};
var newRecorder=null;

function pushAudioStream(){
    setInterval(()=>{
        if(newRecorder===null){
            return false;
        }
        if(newRecorder.isGetStream===false){
            return false;
        }
        if (voiceSocket  && voiceSocket.readyState === WebSocket.OPEN) {
            newRecorder.stop();
            // 将 Float32Array 转换为 PCM 数据
            let pcmBlob=newRecorder.getBlob();
            // 将音频 Blob 转为 Base64
            let reader=new FileReader();
            newRecorder.start();
            reader.onload=()=>{
                let base64Data='';//默认静音
                if($store['muted']===false){
                    base64Data=reader.result.split(",")[1]; // 获取Base64编码的数据部分
                }
                voiceSocket.send(base64Data); // 发送编码后的base64字符串
            };
            reader.readAsDataURL(pcmBlob); // 读取Blob数据
        }
    }, sendInterval);
}

function startVoiceSocket(){
    newRecorder=new Recorder();
    pushAudioStream();
    voiceSocket=new WebSocket("wss://192.168.0.170:30000");
    voiceSocket.onopen=()=>{
        console.log("VoiceSocket 已连接");
    };
    voiceSocket.onmessage=(event)=>{
        if(event.data===''){//静音的数据
            return false;
        }else{
            playAudioData(event.data);
        }
    };
    voiceSocket.onclose=()=>console.log("VoiceSocket 已断开连接");
    voiceSocket.onerror=(error)=>console.error("VoiceSocket 错误:",error);
}

function playAudioData(base64Data){
    // 解码Base64数据为ArrayBuffer
    let binaryString=atob(base64Data);
    let len=binaryString.length;
    let bytes=new Uint8Array(len);
    for (let i=0;i<len;i++){
        bytes[i]=binaryString.charCodeAt(i);
    }
    let audioBlob=new Blob([bytes.buffer],{type:'audio/wav'});

    // 创建新的audio元素
    let audioElement=document.createElement('audio');
    audioElement.src=window.URL.createObjectURL(audioBlob);
    audioElement.autoplay=true;

    // 将audio元素添加到容器中
    layerVoiceContainer.appendChild(audioElement);

    // 监听播放结束事件，清除audio元素
    audioElement.addEventListener('ended', ()=>{
        audioElement.remove();// 播放完后移除该元素
    });

    // 开始播放
    audioElement.play().then(r=>void 0);
}

let startSettingVoice=()=>{
    LayerVoiceOpen.addEventListener(//点击open图标，关闭语音，以禁用
        'click',
        ()=>{
            newRecorder.releaseMicrophone();
            newRecorder=null;
            $store['muted']=true;
        }
    );
    LayerVoiceClose.addEventListener(//点击close图标，启用语音，以启用
        'click',
        ()=>{
            newRecorder=null;
            newRecorder=new Recorder();
            $store['muted']=false;
        }
    );
    if($store['muted']===false){//未禁音
        LayerVoiceClose.style.display='none';
        LayerVoiceOpen.style.display='';
    }else{//已禁音
        LayerVoiceOpen.style.display='none';
        LayerVoiceClose.style.display='';
    }
};

startVoiceSocket();

startSettingVoice();

subscribe('muted',(newValue)=>{
    if(newValue===true){//以禁音
        LayerVoiceClose.style.display='';
        LayerVoiceOpen.style.display='none';
    }else{//以启用
        LayerVoiceClose.style.display='none';
        LayerVoiceOpen.style.display='';
    }
});