// jshint esversion:6
/**
 * ThreeD Class for 3D objects, implementation of three.js
 *
 * @author - D R Noble <dom@domnoble.com>
 */

import {
  Scene,
  PerspectiveCamera,
  WebGLRenderer,
  Mesh,
  AmbientLight,
  DirectionalLight,
  HemisphereLight,
  Color,
} from 'three';
import 'three/examples/js/loaders/GLTF2Loader';
import 'three/examples/js/loaders/STLLoader';
import 'three/examples/js/controls/OrbitControls';
/* global THREE */

export default class ThreeD {
  constructor(
    selector = 'threed',
    modelsJSON = false,
    start = 0,
    color = 0x3949ab,
    specular = 0xffffff,
    background = 0xe8eaf6,
    ambientLight = 0xffffff,
    light = 0xffffff,
    planeColor = 0xc5cae9,
    fova = 45
  ){

    if(document.querySelector('div.' + selector)){
      this.element = document.querySelector('div.' + selector);
    } else {
      this.element = document.getElementById(selector);
    }


    var width = this.element.offsetWidth,
    height = this.element.offsetHeight,
    dist = height / 2 / Math.tan(Math.PI * fova / 360),
    aspect = height / width,
    fov = 2 * Math.atan( ( width / aspect ) / ( 2 * dist ) ) * ( 180 / Math.PI );

    this.h = height;
    this.w = width;
    this.currentPosition = start;
    this.color = color;
    this.specular = specular;
    this.light = light;
    this.ambientLight = ambientLight;
    this.planeColor = planeColor;
    this.background = background;
    this.fov = fov;

    this.container = document.createElement('div');

    if(modelsJSON){
      this.data = JSON.parse(modelsJSON);
    } else {
      this.data = JSON.parse(this.element.dataset.threed);
    }

    this.count = Object.keys(this.data.models).length, this.count--;

    this.modelViewer(start);
    this.loop();

  }

  loop() {
      var loop = () => {
        requestAnimationFrame(loop);
        this.renderer.render(this.scene, this.camera);
        this.controls.update();
      }
      loop();
  }

  modelViewer(){

    var model = this.data.models[this.currentPosition],
    file = model.url,
    fileparts = file.split('.'),
    filetype = fileparts[1];

    this.container.innerHTML = "";

    this.controls = document.createElement('div');
    this.controls.classList.add("threed-controls");

    this.nextbtn = document.createElement('button');
    this.nextbtn.classList.add("btn-floating", "btn-large", "waves-effect", "waves-light", "pull-right", "next");
    this.nextbtni = document.createElement('i');
    this.nextbtni.classList.add("material-icons");
    var nextbtnicontent = document.createTextNode('skip_next');
    this.nextbtni.appendChild(nextbtnicontent);
    this.nextbtn.appendChild(this.nextbtni);
    this.controls.appendChild(this.nextbtn);


    this.prevbtn = document.createElement('button');
    this.prevbtn.classList.add("btn-floating", "btn-large", "waves-effect", "waves-light", "pull-left", "prev");
    this.prevbtni = document.createElement('i');
    this.prevbtni.classList.add("material-icons");
    var prevbtnicontent = document.createTextNode('skip_previous');
    this.prevbtni.appendChild(prevbtnicontent);
    this.prevbtn.appendChild(this.prevbtni);
    this.controls.appendChild(this.prevbtn);
    this.element.appendChild(this.container);

    this.container.appendChild(this.controls);

    this.camera = new PerspectiveCamera(45, this.w / this.h, 0.05, 10000);
    if(filetype == 'glb'){
      this.camera.position.set( 0, 0.2, 0.3);
    } else if(filetype == 'stl') {
      this.camera.position.set( 0, 200, 0.5);
    }


    this.controls = new THREE.OrbitControls(this.camera);
    this.controls.autoRotate = true;
    this.controls.target.set( 0, 0, 0);
    this.controls.update();

    this.scene = new Scene();
    this.scene.background = new Color(this.background);

    this.renderer = new WebGLRenderer();
    this.renderer.shadowMap.enabled = true;
    this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    this.renderer.setSize(this.w, this.h);
    this.renderer.setClearColor(this.background, 1);
    this.renderer.setPixelRatio( window.devicePixelRatio );
    this.renderer.gammaOutput = true;
    this.container.appendChild(this.renderer.domElement);


    var light = new HemisphereLight( this.light );
		light.position.set( 0, 1, 0 );
		this.scene.add( light );

		light = new DirectionalLight( this.light );
		light.position.set( -10, 6, -10 );
		this.scene.add( light );

    this.scene.add(new AmbientLight(this.ambientLight));

    if(filetype == 'glb'){
      this.viewGLTF(file);
    } else if(filetype == 'stl') {
      this.viewSTL(file);
    } else {
      alert("3D model filetype not supported")
    }

    window.addEventListener( 'resize', this.onWindowResize(this.camera,this.renderer,this.element), false );
  }

  viewSTL(file){
    this.camera.position.set( 0, 200, 0.2);

    var loader = new THREE.STLLoader(), scene = this.scene;

    loader.load(file, function(geometry){
      var material = new THREE.MeshNormalMaterial(); // new MeshPhongMaterial({ color: color, specular: specular });
      var mesh = new Mesh(geometry, material);
      // mesh.castShadow = true; //default is false
      // mesh.receiveShadow = true; //default
      scene.add(mesh)
    });

    this.navInit();
  }

  nextObject(){
    if(this.currentPosition < this.count){
      this.currentPosition++;
      this.modelViewer();
    }
  }

  prevObject(){
    if(this.currentPosition > 0) {
      this.currentPosition--;
      this.modelViewer();
    }
  }

  navInit(){
    this.nextButton = this.container.querySelector('button.next');
    this.prevButton = this.container.querySelector('button.prev');
    this.nextButton.onclick = () => this.nextObject();
    this.prevButton.onclick = () => this.prevObject();
  }

  viewGLTF(file){

    var loader = new THREE.GLTF2Loader(), scene = this.scene;

    loader.load(file,
     function( gltf ){
       scene.add( gltf.scene );
       gltf.animations;
       gltf.scene;
       gltf.scenes;
       gltf.cameras;
     });

     this.navInit();

  }

  onWindowResize(camera,renderer,element){
    camera.aspect = element.offsetWidth / element.offsetHeight;
    camera.updateProjectionMatrix();
    renderer.setSize( element.offsetWidth, element.offsetHeight );
  }

}
