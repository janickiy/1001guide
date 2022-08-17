import React from 'react';

const MessageBlock = ({children}) => {
  return (
    <div className="alert alert-success" role="alert">
      {children}
    </div>
  );
};

export default MessageBlock;