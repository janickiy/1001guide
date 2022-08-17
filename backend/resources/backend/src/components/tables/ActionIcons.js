import React from 'react';
import {Link} from 'react-router-dom';

const ActionIcons = ({edit, remove, id, view=null}) => {

  const viewIcon = view ?
    (<a href={view} target="_blank"><i className="fa fa-eye" aria-hidden="true"></i></a>):
    null;

  const removeLink = remove ? (
    <a href="/" className="color-red" onClick={e => {e.preventDefault(); remove(id)}}>
      <i className="fa fa-times" aria-hidden="true"></i>
    </a>
  ) : null;

  return (
    <td className="col-actions">
      <Link to={edit}><i className="far fa-edit"></i></Link>
      {viewIcon}
      {removeLink}

    </td>
  );

};

export default ActionIcons;