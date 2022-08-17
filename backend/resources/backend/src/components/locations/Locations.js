import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";
import BackButton from "../includes/BackButton";

const Locations = ({match}) => {
  const countryId = match.params.id;

  return (
    <div>
      <BackButton/>
      <ButtonAdd />
      <ItemList
        tableData={["name", "country_code", "total_tours"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type={`locations/country/${countryId}/`}
        multilang={true}
      />
    </div>
  )
};

export default Locations;

