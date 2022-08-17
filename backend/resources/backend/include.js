const backendPath = path => `/server/backend${path}`;

const addScriptTag = path => {
  const script = document.createElement('script');
  script.src = backendPath(path);
  document.body.appendChild(script);
};

const loadJS = files => {

  // main js
  addScriptTag(files['main.js']);

  // chink js
  const chunkJsFileBegins = 'static/js/';
  const chunkJs = Object.keys(files).find( filename => {
    if ( filename.includes(chunkJsFileBegins) )
      return true;
  } );
  addScriptTag(files[chunkJs]);
};

const loadCSS = files => {
  const link = document.createElement('link');
  link.href = backendPath(files['main.css']);
  link.rel = 'stylesheet';
  document.head.appendChild(link);
};

const loadScripts = async () => {
  const jsonFile = await fetch('/server/backend/asset-manifest.json');
  const {files} = await jsonFile.json();
  loadCSS(files);
  loadJS(files);
};

loadScripts();
