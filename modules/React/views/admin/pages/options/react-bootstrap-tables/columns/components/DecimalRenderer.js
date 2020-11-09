import React from 'react';
import PropTypes from 'prop-types';

class DecimalRenderer extends React.Component {
    static propTypes = {
        onUpdate: PropTypes.func.isRequired
    }

    constructor(props) {
        super(props);
        this.onChange = this.onChange.bind(this);
    }

    componentDidMount() {
        this.decimal.focus();
    }
    componentWillUnmount() {
        this.decimal.blur();
    }
    getValue() {
        var num = parseFloat(this.decimal.value).toFixed(4);
        if(num != num) {
            return 0;
        }
        return num;
    }

    onChange() {
        var num = parseFloat(this.decimal.value).toFixed(4);
        if(num != num) {
            return 0;
        }
        return num;
    }

    render() {
        const { value, onUpdate, className, rowIndex, ...rest } = this.props;
        
        return (
            <>
                <input onChange={this.onChange} className={className + " form-control editor edit-text"} key={'decimal'} ref={(node) => { this.decimal = node; }} type="text" {...rest} />
            </>
        );
    }
}

export default DecimalRenderer;