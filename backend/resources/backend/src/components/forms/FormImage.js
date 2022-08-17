import React, {useState, useRef} from 'react';
import axios from 'axios';
import {routerUrl, siteUrl} from "../../helpers/client-server";
import Loading from '../Loading';

const FormImage = ({name, value, initImage, setValue, size="1000x1500"}) => {

  const [image, setImage] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [errorText, setErrorText] = useState(null);

  const imgInput = useRef(null);


  const handleImagePick = () => {
    setIsLoading(true);
    const file = imgInput.current.files[0];
    if ( !file ) return;

    let data = new FormData();
    data.set("image", file);
    data.set("action", "image_upload");
    data.set("size", size);

    axios.post(routerUrl, data)
    .then(response => {
      if ( response.data.error )
        setErrorText(response.data.error);
      else {
        setImage(response.data.image);
        setValue(name, response.data.id);
      }
      setIsLoading(false);
    })
    .catch(error => {
      setErrorText(error.toString());
      setIsLoading(false);
    });
  };

  const imagePreview = (
    <div className="image-preview">
      {(image || initImage) ? (<img src={siteUrl + (image || initImage)} alt=""/>) : "Изображение не выбрано"}
    </div>
  );

  const loadedContent = isLoading ?
    (<Loading/>) :
    errorText || imagePreview;

  return (
    <div className="card image-uploader mb-3">
      <div className="card-body">
        <div className="form-group">
          <label>Выберите изображение</label>
          <input type="file" className="form-control-file" ref={imgInput} onChange={handleImagePick} />
          <input type="hidden" name={name} value={value} onChange={setValue} />
        </div>
        {loadedContent}
      </div>
    </div>
  );

};

export default FormImage;