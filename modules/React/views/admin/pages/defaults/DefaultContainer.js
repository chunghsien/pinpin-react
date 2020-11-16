
import React from 'react';

const DefaultContainer = () => {
    const href = location.href
    return (
        <div>
        <h3>Default container: </h3>
        <span className="text-muted">{href}</span>
        </div>
    );
}
export default DefaultContainer;