import axios from 'axios';

const routerUrl = "https://qwart.digital/server/api/";
const siteUrl = "https://qwart.digital/server";
const uploadDir = siteUrl + "/upload/";

/**
 * Send request to server
 *
 * @param {object} dataToSend
 * @param {string} url
 * @return {AxiosPromise<any>}
 */
const sendRequest = (url, method='get', dataToSend = null, noFormData=false) => {
  console.log(url, method, noFormData);
  if ( noFormData )
    return axios[method](
      routerUrl+url,
      dataToSend
    );

  // if ( url.includes("poi") ) {
  //   console.log("POI!");
  //   return axios.post(routerUrl+"poi/?lang=en", dataToSend);
  // }

  let data = new FormData();
  if ( dataToSend ) {
    Object.keys(dataToSend).forEach(function (key) {
      data.set(key, dataToSend[key]);
    });
  }
  return axios[method](
    routerUrl+url,
    data
  )
};


export {routerUrl, siteUrl, uploadDir, sendRequest};
