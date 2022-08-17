import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const TemplateValueCreate = ({match}) => {
  const {template_id} = match.params;

  return (
    <div className="create-page">
      <h1>Новый блок текста</h1>
      <FormEdit
        fieldsToShow={fieldsToShow}
        itemType={`templates/fields/${template_id}/`}
        actionType="save"
        multilang={true}
      />
    </div>
  );

};

export default TemplateValueCreate;