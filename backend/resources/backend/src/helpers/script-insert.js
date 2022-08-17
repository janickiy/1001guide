const nodeScriptIs = node => {
  return node.tagName === 'SCRIPT';
};


const nodeScriptClone = node => {
  const script  = document.createElement("script");
  script.text = node.innerHTML;
  for ( let i = node.attributes.length-1; i >= 0; i-- ) {
    script.setAttribute( node.attributes[i].name, node.attributes[i].value );
  }
  return script;
};


const nodeScriptReplace = node => {
  if ( nodeScriptIs(node) === true ) {
    node.parentNode.replaceChild( nodeScriptClone(node) , node );
  }
  else {
    let i = 0;
    const children = node.childNodes;
    while ( i < children.length ) {
      nodeScriptReplace( children[i++] );
    }
  }

  return node;
};


export {nodeScriptReplace};