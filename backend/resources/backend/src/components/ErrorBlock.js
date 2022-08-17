import React from 'react';

const ErrorBlock = ({children}) => {
  return (
    <div className="alert alert-danger" role="alert">
      {children}
    </div>
  );
};

export default ErrorBlock;