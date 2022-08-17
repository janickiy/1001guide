import React from 'react';

const Loading = () => {
  return (
    <div className="text-center loader-block">
      <div className="spinner-border text-success" role="status">
        <span className="sr-only">Loading...</span>
      </div>
    </div>
  );
};

export default Loading;