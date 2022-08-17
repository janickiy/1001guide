import {sendRequest} from './client-server';

const getFieldType = (id) => {

  return new Promise( resolve => {

    sendRequest({action: 'get_field_type', id: id})
    .then(response => {
      if ( response.data.hasOwnProperty('type') ) {
        resolve(response.data['type']);
      }
      resolve(null);
    });

  } );



};

export {getFieldType};