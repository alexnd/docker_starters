// Web Radio
var app = {
  streamUrl: 'https://uk2.internet-radio.com/proxy/ambientradio?mp=/stream;',
  el_playBtn: null,
  el_btnTxt: null,
  el_statusTxt: null,
  el_volumeCon: null,
  el_volumeInp: null,
  snd: null,
  onAir: false,
  trackId: '',
  LBL: {
    play: 'Play Radio&gt;&gt;',
    stop: '#Stop Radio',
    stopped: 'TURNED OFF',
    playing: '<b>ON AIR</b>',
    offline: '<i>OFFLINE</i>'
  },
  init: function () {
    this.el_btnTxt = document.getElementById('playbtn_txt');
    this.el_statusTxt = document.getElementById('status_txt');
    this.el_volumeCon = document.getElementById('c_player_volume');
    this.el_volumeInp = document.getElementById('player_volume');
    this.el_playBtn = document.getElementById('btn_play_radio');
    if (this.el_playBtn) this.el_playBtn.addEventListener('click', function(e) {
      e.preventDefault();
      app.playRadio();
      return false;
    });
    if (this.el_volumeInp) this.el_volumeInp.addEventListener('change', function(e) {
      app.setVolume(e.target.value * .01)
    });
  },
  playRadio: function() {
    console.log('*[play]');
    if (this.onAir) {
      return this.stopRadio();
    }
    this.snd = new Howl({
      src: this.streamUrl,
      html5: true,
      format: ['mp3', 'aac']
    });
    this.snd.play();
    this.el_volumeInp.value = this.snd.volume() * 100;
    this.el_btnTxt.innerHTML = this.LBL.stop;
    this.el_statusTxt.innerHTML = this.LBL.playing;
    this.el_volumeCon.style.display = 'inline-block';
    // TODO: set button color green
    this.onAir = true;
  },
  stopRadio: function() {
    console.log('*[stop]');
    this.snd.stop();
    this.snd.unload();
    this.el_btnTxt.innerHTML = this.LBL.play;
    this.el_statusTxt.innerHTML = this.LBL.stopped;
    this.el_volumeCon.style.display = 'none';
    // TODO: set button color red
    // this.toggleClass(this.el_, 'red-100')
    this.onAir = false;
  },
  setVolume(v) {
    if (!this.onAir) return;
    this.snd.volume(v);
  },
  toggleClass: function (el, className) {
    var a = el.className.split(/\s/);
    var i = a.indexOf(className);
    if (i === -1) {
      a.push(className);
    } else {
      a.splice(i, 1);
    }
    el.className = a.join(' ');
  }
}

window.onload = function() {
  console.log('*[onload]');
  app.init();
}
