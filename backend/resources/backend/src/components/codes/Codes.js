import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Codes = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["name"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="codes"
      />
    </div>
  )
};

export default Codes;

