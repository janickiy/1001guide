import React from 'react';
import {withRouter} from 'react-router-dom';

const BackButton = (props) => {
  const goBack = (e) => {
    e.preventDefault();
    props.history.goBack();
  };
  return (
    <div className="back-btn">
      <a href="/" className="btn btn-light" onClick={goBack}>&larr; Назад</a>
    </div>
  );
};

export default withRouter(BackButton);