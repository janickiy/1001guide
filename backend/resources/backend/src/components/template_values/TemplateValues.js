import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const TemplateValues = ({match}) => {
  const templateId = match.params.id;

  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["value", "order"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type={`templates/fields/${templateId}`}
        multilang={true}
      />
    </div>
  )
};

export default TemplateValues;

