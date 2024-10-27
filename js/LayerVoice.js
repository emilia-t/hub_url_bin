async function getMicrophoneStream() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        console.log("已获取麦克风音频流");
        return stream;
    } catch (error) {
        console.error("获取麦克风失败：", error);
    }
}
setTimeout(
    ()=>{
        getMicrophoneStream().then(r => console.log(r))
    }
    ,500
);