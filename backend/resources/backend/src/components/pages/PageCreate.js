import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const PageCreate = () => {

  return (
    <div className="create-page">
      <h1>Новая страница</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="pages" actionType="save" multilang={true} />
    </div>
  );

};

export default PageCreate;