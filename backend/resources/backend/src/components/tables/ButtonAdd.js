import React from 'react';
import {Link} from 'react-router-dom';

const ButtonAdd = ({link="create/"}) => {

  return (
    <p>
      <Link to={link} className="btn btn-success mb-3">+ Добавить</Link>
    </p>
  );

};

export default ButtonAdd;