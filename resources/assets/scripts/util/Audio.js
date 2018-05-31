// jshint esversion:6
/* eslint-disable no-unused-vars */

import getAverageColor from 'get-average-color'

/**
 * Audio.js
 * todo : equalizer, mute, repeat, shuffle,
 * Class for easy displaying and more complete styling of HTML Audio
 * @author Dom Noble <dom@domnoble.com>
 */

 export default class Audio {

   /*
   *
   * Constructor function to select the audio html and set basic global options
   *
   */
   constructor(
     selectorClass   = '',
     audioJSON = false,
     skipAutoPlay = false,
     autoPlay = false,
     startTrack = 0,
     primaryColor    = '#283593',
     secondaryColor  = '#a1aae6'
   ){
     this.selectorClass = selectorClass;
     this.primaryColor = primaryColor;
     this.secondaryColor = secondaryColor;
     this.trackDuration = 0;
     this.audioStopped = false;
     this.buttonClick = false;
     this.audioInit = null;
     this.audioEnd = false;
     this.tracks = null;
     this.currentTrack = startTrack;
     this.source = null;
     this.startTime = 0;
     this.resume = null;
     this.currentPosition = 0;
     this.audio = audioJSON;
     if(audioJSON){
       this.audioPlay = document.querySelector(this.selectorClass);
       this.audioInfo = JSON.parse(audioJSON);
       if(this.audioPlay.querySelector('.audio-cover')){
         this.coverCarousel();
       }
     }
     this.audioPlayer(this.audio,0,skipAutoPlay,autoPlay)
   }

   /**
    * Main audioPlayer function, accepts a list of audio tracks in JSON format and builds an audio player using the Web Audio API
    * @author Dom Noble <dom@domnoble.com>
    */
    audioPlayer(
      audio = false,
      currentTrack = 0,
      skipAutoPlay = true,
      autoPlay = false
    ){

      // set default settings
      this.frequencyBarsEnable = null,
      this.oscilloscopeEnable = null,
      this.infoEnable = null,
      this.trackDuration = 0,
      this.currentPosition = 0,
      this.position = 0,
      this.startTime = 0,
      this.audioInit = false,
      this.audioEnd = false,
      this.resume = false,
      this.playing = false,
      this.currentTrack = currentTrack,
      this.skipAutoPlay = skipAutoPlay,
      this.autoPlay = autoPlay,
      this.audioRaw = audio;

      try {
        // Fix up prefixing
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        this.context = new AudioContext();
      }
      catch(e) {
        alert('Web Audio API is not supported in this browser');
      }

      var audioInfo;
      var audioCount;

      if(audio){
        this.audioPlay = document.querySelector(this.selectorClass);
        audioInfo = JSON.parse(audio);
        audioCount = audioInfo.tracks.length;
        this.audioInfo = audioInfo;
      } else if (this.audio) {
        this.audioPlay = document.querySelector(this.selectorClass);
        audioInfo = JSON.parse(this.audio);
        audioCount = audioInfo.tracks.length;
        this.audioInfo = audioInfo;
      }

      this.tracks = audioInfo.tracks.length;
      this.tracks--;
      this.playButton = this.audioPlay.querySelector('button.play');
      this.audioPosition = this.audioPlay.querySelector('.audio-position input[type=range]');

      this.gainNode = this.context.createGain();

      if (this.audioPlay.querySelector('.oscilloscope')){
        this.audioOscilloscope(this);
      }

      if (this.audioPlay.querySelector('.frequency-bars')){
        this.audioFrequencyBars(this);
      }

      if (this.audioPlay.querySelector('.audio-info')){
        this.audioInformation(this);
      }

      if(this.audioPlay.querySelector('button.stop')){
        this.stopButton = this.audioPlay.querySelector('button.stop');
        this.stopButton.onclick = () => this.stopAudio(true);
      }

      if(this.audioPlay.querySelector('button.next')){
        this.nextButton = this.audioPlay.querySelector('button.next');
        if(this.currentTrack == this.tracks){
          this.nextButton.classList.add("disabled");
          this.nextButton.disabled = true;
        }
        if(this.currentTrack < this.tracks){
          this.nextButton.classList.remove("disabled");
          this.nextButton.disabled = false;
          this.nextButton.onclick = () => this.nextAudio();
        }
      }

      if(this.audioPlay.querySelector('button.prev')){
        this.prevButton = this.audioPlay.querySelector('button.prev');
        if(this.currentTrack == 0){
          this.prevButton.classList.add("disabled");
          this.prevButton.disabled = true;
        }
        if(this.currentTrack > 0){
         this.prevButton.classList.remove("disabled");
         this.prevButton.disabled = false;
         this.prevButton.onclick = () => this.prevAudio();
        }
      }

      if(this.audioPlay.querySelector('div.volume')){
        this.volWrap = this.audioPlay.querySelector('.volume');
        this.volButton = this.audioPlay.querySelector('.volume > a');
        this.volDrop = this.audioPlay.querySelector('.volume > .dropdown-content');
        this.volInput = this.audioPlay.querySelector('.volume > .dropdown-content > li > .range-field > input');

        if(this.getCookie('mxvol')){
          this.volInput.value = this.getCookie('mxvol');
          this.gainNode.gain.value = this.getCookie('mxvol');
        } else {
          this.volInput.value = 1;
          this.gainNode.gain.value = 1;
        }

        this.volButton.onclick = () => {
          this.volDrop.classList.toggle("active");
        };

        /**
         *
         * Detect Volume Range Change and set gainNode
         *
         */
        this.volInput.addEventListener('input', function(event){
          thisMedia.gainNode.gain.value = this.value;
          var currGain = thisMedia.gainNode.gain.value;
          thisMedia.gainNode.gain.setValueAtTime(currGain, thisMedia.context.currentTime + 1);
          thisMedia.setCookie('mxvol',this.value,1);
        }, true);

        document.addEventListener('click', (event) => {
          if(this.volDrop.classList.contains("active")) {
            this.volDrop.classList.remove("active");
          }
        }, true);

      }




      this.context.ended = this.endedAudio();

      this.playButton.onclick = () => this.playAudio(this.oAnalyser,this.fAnalyser,0);

      if(this.autoPlay){
        this.playButton.className = this.playButton.className.replace(/\bplay\b/g, "pause");
        this.pauseButton = this.audioPlay.querySelector('button.pause');
        if(this.audioPlay.querySelector('button.pause i').innerHTML !== "pause"){
          this.audioPlay.querySelector('button.pause i').innerHTML = "pause";
        }
        this.playAudio(this.oAnalyser,this.fAnalyser,0);
        this.pauseButton.onclick = () => this.pauseAudio();
      }

      if(this.audioPlay.querySelector('.audio-cover')){
        var activeCover;
        currentTrack = this.currentTrack;
        activeCover = this.audioPlay.querySelector('.track-' + currentTrack);
        getAverageColor(activeCover.src).then(rgb => {
          this.audioPlay.querySelector('.audio-cover').style.backgroundColor = 'rgb('+rgb.r+','+rgb.g+','+rgb.b+')';
        }) // { r: 66, g: 83, b: 25 }
        $(document).ready(function(){
          $('.covers').carousel('set', currentTrack);
        });
      }

      if(this.audioPlay.querySelector('div.audio-playlist')){
        this.audioPlay.querySelector('div.audio-playlist').innerHTML = '<table class="bordered centered highlight playlist-table"><tbody></tbody></table>';
        var playlist,
        playliPre = '',
        playliCover,
        trackselector;
        this.audioPlay.querySelector('table.playlist-table tbody').innerHTML = '';
        for  (var i = 0; i < audioInfo.tracks.length; i++) {
          if(i == this.currentTrack){
            playlist = '<tr><td><button id="track-' + i + '" data-id="' + i + '" class="waves-light btn-floating playlist-play disabled"><i class="material-icons">equalizer</i></button></td><td>' + audioInfo.tracks[i].title + '</td><td>' + audioInfo.tracks[i].artist + '</td><td>' + audioInfo.tracks[i].album + '</td></tr>';
          } else {
            playlist = '<tr><td><button id="track-' + i + '" data-id="' + i + '" class="waves-light btn-floating playlist-play"><i class="material-icons">play_arrow</i></button></td><td>' + audioInfo.tracks[i].title + '</td><td>' + audioInfo.tracks[i].artist + '</td><td>' + audioInfo.tracks[i].album + '</td></tr>';
          }
          this.audioPlay.querySelector('table.playlist-table tbody').insertAdjacentHTML('beforeend', playlist);
        }

        var playlistPlay = this.audioPlay.querySelectorAll(".playlist-play"),t;
        playlistPlay.forEach(function(element) {
           t = element.dataset.id;
           element.addEventListener('click', function(event){
             thisMedia.setTrack(this.dataset.id);
           }, true);
        });
      }

      var thisMedia = this;

      /**
       *
       * Detect Audio Range input and set position
       *
       */
      this.audioPosition.addEventListener('input', function(event){
        var thisTime = this.value;
        if(thisMedia.playing){
          thisMedia.stopAudio(false);
          thisMedia.audioEnd = true;
          thisMedia.audioPlayer(thisMedia.audioRaw,thisMedia.currentTrack);
          setTimeout(function(){
            thisMedia.playAudio(thisMedia.oAnalyser,thisMedia.fAnalyser,thisTime);
            thisMedia.currentPosition = thisTime;
          },100);
        }
      }, true);

    }

    audioProgress(){
      var thisMedia = this;
      var fid = setInterval(frame, 100);
      function frame(){
        if (thisMedia.audioEnd && thisMedia.audioInit) {
          thisMedia.context.close();
          clearInterval(fid);
          if(!thisMedia.buttonClick && thisMedia.currentTrack < thisMedia.tracks){
            thisMedia.nextAudio();
          }
        } else {
          thisMedia.audioPosition.setAttribute('min',0);
          thisMedia.audioPosition.setAttribute('max',thisMedia.trackDuration);
          thisMedia.audioPosition.value = thisMedia.correctTime;
        }
      }
    }

    /**
    * Get, Set and Erase Cookie taken from https://www.quirksmode.org/js/cookies.html
    * @author quirksmode
    */
    setCookie(name,value,days) {
      var expires = "";
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    getCookie(name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
      }
      return null;
    }

    eraseCookie(name) {
      document.cookie = name+'=; Max-Age=-99999999;';
    }

    /**
    * Checks to see if an image exists
    * @author Dom Noble
    */
    imageExists(url) {
      var xhr = new XMLHttpRequest();
      xhr.open('HEAD', url, false);
      xhr.send();

      if (xhr.status == "404") {
          return false;
      } else {
          return true;
      }
    }

    /**
    * runs a loop to display a frequency bar visualization visualization
    * taken and adapted from voice-change-o-matic Web Audio API example
    * @author Chris Mills
    */
    audioFrequencyBars(thisMedia){
      this.frequencyBarsEnable = true;
      this.fCanvas = this.audioPlay.querySelector('.frequency-bars');
      this.fCanvasCtx = this.fCanvas.getContext("2d");
      this.fAnalyser = this.context.createAnalyser();
      this.altIntendedWidth = this.fCanvas.offsetWidth;
      this.fAnalyser.fftSize = 256;
      this.fAnalyser.minDecibels = -90;
      this.fAnalyser.maxDecibels = -10;
      this.fAnalyser.smoothingTimeConstant = 0.85;
      this.bufferLengthAlt = this.fAnalyser.frequencyBinCount;
      this.dataArrayAlt = new Uint8Array(this.bufferLengthAlt);
      this.fCanvas.setAttribute('width',this.altIntendedWidth);

      const WIDTH = this.fCanvas.width;
      const HEIGHT = this.fCanvas.height;

      this.fCanvasCtx.clearRect(0, 0, WIDTH, HEIGHT);

      thisMedia = this;

      var frequencyBars = function() {

        var drawVisualAlt = requestAnimationFrame(frequencyBars);

        thisMedia.fAnalyser.getByteFrequencyData(thisMedia.dataArrayAlt);

        thisMedia.fCanvasCtx.fillStyle = thisMedia.secondaryColor;
        thisMedia.fCanvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

        var barWidth = (WIDTH / thisMedia.bufferLengthAlt) * 2.5;
        var barHeight;
        var v = 0;

        for(var i = 0; i < thisMedia.bufferLengthAlt; i++) {
          barHeight = thisMedia.dataArrayAlt[i];

          thisMedia.fCanvasCtx.fillStyle = thisMedia.primaryColor;
          thisMedia.fCanvasCtx.fillRect(v,HEIGHT-barHeight/2,barWidth,barHeight/2);

          v += barWidth + 1;
        }
      };
      frequencyBars();
    }

    /**
    * runs a loop to display oscilloscope visualization
    * taken and adapted from voice-change-o-matic Web Audio API example
    * @author Chris Mills
    */
    audioOscilloscope(thisMedia){
      this.oscilloscopeEnable = true;
      this.oCanvas = thisMedia.audioPlay.querySelector('.oscilloscope');
      this.oCanvasCtx = this.oCanvas.getContext("2d");
      this.intendedWidth = this.oCanvas.offsetWidth;
      this.oAnalyser = this.context.createAnalyser();
      this.bufferLength = this.oAnalyser.frequencyBinCount;
      this.dataArray = new Uint8Array(this.bufferLength);
      this.oAnalyser.fftSize = 2048;
      this.oAnalyser.minDecibels = -90;
      this.oAnalyser.maxDecibels = -10;
      this.oAnalyser.smoothingTimeConstant = 0.85;
      this.oAnalyser.getByteTimeDomainData(this.dataArray);
      this.oCanvas.setAttribute('width',this.intendedWidth);

      const WIDTH = this.oCanvas.width;
      const HEIGHT = this.oCanvas.height;

      thisMedia = this;

      var oscilloscope = function() {

        var drawVisual = requestAnimationFrame(oscilloscope);

        thisMedia.oAnalyser.getByteTimeDomainData(thisMedia.dataArray);

        thisMedia.oCanvasCtx.fillStyle = thisMedia.secondaryColor;
        thisMedia.oCanvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

        thisMedia.oCanvasCtx.lineWidth = 2;
        thisMedia.oCanvasCtx.strokeStyle = thisMedia.primaryColor;

        thisMedia.oCanvasCtx.beginPath();

        var sliceWidth = WIDTH * 1.0 / thisMedia.bufferLength;
        var x = 0;

        for(var i = 0; i < thisMedia.bufferLength; i++) {

          var v = thisMedia.dataArray[i] / 128.0;
          var y = v * HEIGHT/2;

          if(i === 0) {
            thisMedia.oCanvasCtx.moveTo(x, y);
          } else {
            thisMedia.oCanvasCtx.lineTo(x, y);
          }

          x += sliceWidth;
        }

        thisMedia.oCanvasCtx.lineTo(thisMedia.oCanvas.width, thisMedia.oCanvas.height/2);
        thisMedia.oCanvasCtx.stroke();
      };
      oscilloscope();
    }

    /**
    * Populates audio-info canvas
    * @author Dom Noble
    */
    audioInformation(thisMedia){
      this.infoEnable = true;
      this.aCanvas = this.audioPlay.querySelector('.audio-info');
      this.aCanvasCtx = this.aCanvas.getContext("2d");
      this.aIntendedWidth = this.aCanvas.offsetWidth;
      this.aCanvas.setAttribute('width',this.aIntendedWidth);
      const WIDTH = this.aCanvas.width;
      const HEIGHT = this.aCanvas.height;

      thisMedia = this;
      var information = function() {

        thisMedia.aCanvasCtx.fillStyle = thisMedia.secondaryColor;
        thisMedia.aCanvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
        thisMedia.aCanvasCtx.font = "12px Roboto";
        thisMedia.aCanvasCtx.fillStyle = thisMedia.primaryColor;
        thisMedia.aCanvasCtx.fillText("Title : " + thisMedia.audioInfo.tracks[thisMedia.currentTrack].title,14,14);
        thisMedia.aCanvasCtx.fillText("Album : " + thisMedia.audioInfo.tracks[thisMedia.currentTrack].album,14,28);
        thisMedia.aCanvasCtx.fillText("Artist : " + thisMedia.audioInfo.tracks[thisMedia.currentTrack].artist,14,42);

        if(thisMedia.context.currentTime && thisMedia.audioInit && thisMedia.startTime && thisMedia.trackDuration){
          thisMedia.correctTime = thisMedia.context.currentTime - thisMedia.startTime;
          if(thisMedia.currentPosition){
            thisMedia.correctTime = thisMedia.correctTime + Number(thisMedia.currentPosition);
          }

          thisMedia.currentPercent = Math.floor(thisMedia.correctTime);
          thisMedia.onePercent = thisMedia.trackDuration / 100;
          thisMedia.percent = thisMedia.correctTime / thisMedia.onePercent;

          if(thisMedia.percent < 50){
            thisMedia.percent = thisMedia.percent + 0.8;
          }

          thisMedia.aCanvasCtx.fillText("Current Time - " + thisMedia.secondsToTime(thisMedia.correctTime),402,14);
          thisMedia.aCanvasCtx.fillText("Duration - " + thisMedia.secondsToTime(thisMedia.trackDuration),427,42);

          if(thisMedia.correctTime >= thisMedia.trackDuration){
            thisMedia.audioEnd = true;
          }
        }
        var drawInfo = requestAnimationFrame(information);
      };
      information();
    }

    coverCarousel(){
      var playliCoverExt,
      playliCover,
      carousel = '<div class="carousel covers"></div>',
      carouselItem;

      this.audioPlay.querySelector('.audio-cover').innerHTML = '';
      this.audioPlay.querySelector('.audio-cover').insertAdjacentHTML('beforeend', carousel);

      for  (var i = 0; i < this.audioInfo.tracks.length; i++) {
        playliCoverExt = '-mp3-image.' + this.audioInfo.tracks[i].ext;
        playliCover = this.audioInfo.tracks[i].url.replace('.mp3', playliCoverExt);
        if(this.audioInfo.tracks[i].ext == 'none'){
          playliCover = '/app/themes/matx/dist/images/no-cover.png';
        }

        carouselItem = '<a class="carousel-item"><img  class="track-' + i + '" src="'+ playliCover +'"></a>';

        this.audioPlay.querySelector('.audio-cover .carousel').insertAdjacentHTML('beforeend', carouselItem);

      }

      var currentTrack = this.currentTrack;

      $(document).ready(function(){
        $('.covers').carousel();
        $('.covers').carousel('set', currentTrack);
      });

    }

    secondsToTime(secondsTotal){
      var minutes = Math.floor(secondsTotal / 60);
      var seconds = Math.floor(secondsTotal - minutes * 60);
      var append;
      if(seconds < 10){
        append = "0";
      } else {
        append = "";
      }
      return minutes + ":" + append + seconds;
    }

    timeToSeconds(time){
      time.split(":");
      var minutes = time[0] * 60;
      var seconds = time[1];
      var totalseconds = minutes + seconds;
      return totalseconds;
    }

    getAudio(url, currentPosition){
      return new Promise((resolve, reject) => {
        window.fetch(url)
          .then(response => response.arrayBuffer())
          .then(arrayBuffer =>
            this.context.decodeAudioData(arrayBuffer)
          )
          .then(audioBuffer => {
            this.playButton.disabled = false;
            if(!this.source.buffer){
              this.source.buffer = audioBuffer;
            }
            this.trackDuration = audioBuffer.duration;
            this.startTime = this.context.currentTime;
            this.source.start(this.context.currentTime, currentPosition, this.trackDuration);
            resolve(audioBuffer);
          });
      });
    }

    /**
     *
     * Play function for audio player feed in url and analyser objects
     *
     */
    playAudio(oAnalyser, fAnalyser, currentPosition){
      this.audioStopped = false;
      this.buttonClick = false;
      this.audioEnd = false;
      this.playing = true;
      this.source = this.context.createBufferSource();

      if(this.frequencyBarsEnable && this.oscilloscopeEnable){
        this.source.connect(this.gainNode);
        this.gainNode.connect(oAnalyser);
        oAnalyser.connect(fAnalyser);
        fAnalyser.connect(this.context.destination);
      } else if (!this.oscilloscopeEnable && this.frequencyBarsEnable){
        this.source.connect(this.gainNode);
        this.gainNode.connect(fAnalyser);
        fAnalyser.connect(this.context.destination);
      } else if (this.oscilloscopeEnable && !this.frequencyBarsEnable) {
        this.source.connect(this.gainNode);
        this.gainNode.connect(oAnalyser);
        oAnalyser.connect(this.context.destination);
      } else if (!this.oscilloscopeEnable && !this.frequencyBarsEnable) {
        this.source.connect(this.gainNode);
        this.gainNode.connect(this.context.destination)
      }

      this.getAudio(this.audioInfo.tracks[this.currentTrack].url, currentPosition)
          .then(() => {
            this.audioInit = true;
            this.audioProgress();
          });

      this.playButton.className = this.playButton.className.replace(/\bplay\b/g, "pause");
      this.pauseButton = this.audioPlay.querySelector('button.pause');
      this.audioPlay.querySelector('button.pause i').innerHTML = "pause";
      this.pauseButton.onclick = () => this.pauseAudio();
      if(this.stopButton.classList.contains("disabled")){
        this.stopButton.classList.remove("disabled");
        this.stopButton.disabled = false;
      }
    }

    pauseAudio(){
      if(this.context.state === 'running') {
        this.context.suspend();
        this.pauseButton.className = this.pauseButton.className.replace(/\bpause\b/g, "resume");
        this.resButton = this.audioPlay.querySelector('button.resume');
        this.audioPlay.querySelector('button.resume i').innerHTML = "play_arrow";
      }
      this.resume = true;
      this.resButton.onclick = () => this.resumeAudio();
    }

    resumeAudio(){
      if(this.context.state === 'suspended') {
        this.context.resume();
        this.resButton.className = this.resButton.className.replace(/\bresume\b/g, "pause");
        this.pauseButton = this.audioPlay.querySelector('button.pause');
        this.audioPlay.querySelector('button.pause i').innerHTML = "pause";
      }
      this.resume = false;
      this.pauseButton.onclick = () => this.pauseAudio();
    }

    stopAudio(ap){
      this.audioStopped = true;
      this.buttonClick = true;
      this.playing = false;
      this.context.close();
      if(this.audioPlay.querySelector('button.pause')){
        this.pauseButton.className = this.pauseButton.className.replace(/\bpause\b/g, "play");
        if(this.audioPlay.querySelector('button.play i').innerHTML !== "play_arrow"){
          this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
        }
      } else if(this.audioPlay.querySelector('button.resume')){
        this.resButton.className = this.resButton.className.replace(/\bresume\b/g, "play");
        if(this.audioPlay.querySelector('button.play i').innerHTML !== "play_arrow"){
          this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
        }
      }
      if(ap){
        this.audioPlayer(this.audioRaw,this.currentTrack);
      }
    }

    nextAudio(){
      this.buttonClick = true;
      this.context.close();
      this.currentTrack++;
      if(this.audioPlay.querySelector('button.pause')){
        this.pauseButton.className = this.pauseButton.className.replace(/\bpause\b/g, "play");
      } else if(this.audioPlay.querySelector('button.play') && !this.resume){
        this.playButton = this.audioPlay.querySelector('button.play');
        this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
      } else if (this.audioPlay.querySelector('button.resume') && this.resume){
        this.playButton.className = this.resButton.className.replace(/\bresume\b/g, "play");
      }
      this.audioPlayer(this.audioRaw,this.currentTrack);
      if(this.skipAutoPlay){
        if(this.audioPlay.querySelector('button.play')){
          this.playButton.className = this.playButton.className.replace(/\bplay\b/g, "pause");
        }
        if(this.audioPlay.querySelector('button.pause')){
          this.pauseButton = this.audioPlay.querySelector('button.pause');
          this.audioPlay.querySelector('button.pause i').innerHTML = "pause";
        }
        this.playAudio(this.oAnalyser,this.fAnalyser,0);
        this.pauseButton.onclick = () => this.pauseAudio();
      }
      this.resume = false;
    }

    prevAudio(){
      this.buttonClick = true;
      this.context.close();
      this.currentTrack--;
      this.pauseButton.className = this.pauseButton.className.replace(/\bpause\b/g, "play");
      this.playButton = this.audioPlay.querySelector('button.play');
      if(this.audioPlay.querySelector('button.play i').innerHTML !== "play_arrow"){
        this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
      }
      this.audioPlayer(this.audioRaw,this.currentTrack);
      if(this.skipAutoPlay){
        this.playButton.className = this.playButton.className.replace(/\bplay\b/g, "pause");
        this.pauseButton = this.audioPlay.querySelector('button.pause');
        if(this.audioPlay.querySelector('button.pause i').innerHTML !== "pause"){
          this.audioPlay.querySelector('button.pause i').innerHTML = "pause";
        }
        this.playAudio(this.oAnalyser,this.fAnalyser,0);
        this.pauseButton.onclick = () => this.pauseAudio();
      }
    }

    setTrack(track){
      this.buttonClick = true;
      this.context.close();
      this.currentTrack = track;
      if(this.audioPlay.querySelector('button.pause')){
        this.pauseButton.className = this.pauseButton.className.replace(/\bpause\b/g, "play");
        if(this.audioPlay.querySelector('button.play i').innerHTML !== "play_arrow"){
          this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
        }
      } else if(this.audioPlay.querySelector('button.resume')){
        this.resButton.className = this.resButton.className.replace(/\bresume\b/g, "play");
        if(this.audioPlay.querySelector('button.play i').innerHTML !== "play_arrow"){
          this.audioPlay.querySelector('button.play i').innerHTML = "play_arrow";
        }
      }
      this.audioPlayer(this.audioRaw,this.currentTrack);
      this.playAudio(this.oAnalyser,this.fAnalyser,0);
      this.pauseButton.onclick = () => this.pauseAudio();
    }

    endedAudio(){
       this.currentPosition = 0;
       this.startTime = 0;
       this.audioEnd = true;
       this.audioPosition.value = 0;
       this.stopButton = this.audioPlay.querySelector('button.stop');
       this.stopButton.classList.add("disabled");
       this.stopButton.disabled = true;
    }

    muteAudio(){

    }

 }
