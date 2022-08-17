import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const TemplateValueEdit = ({match}) => {
  const {template_id} = match.params;
  return (
    <div className="edit-page">
      <h1>Редактирование блока текста</h1>
      <FormEdit
        fieldsToShow={fieldsToShow}
        itemType={`templates/fields/${template_id}`}
        multilang={true}
        itemId={Number(match.params.id)}
      />
    </div>
  );

};

export default TemplateValueEdit;