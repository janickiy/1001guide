import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const TagsCreate = () => {

  return (
    <div className="create-page">
      <h1>Новый тег</h1>
      <FormEdit
        fieldsToShow={fieldsToShow} itemType="tags" actionType="save"
        multilang={true}
      />
    </div>
  );

};

export default TagsCreate;