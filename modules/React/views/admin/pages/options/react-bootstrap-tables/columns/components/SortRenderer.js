import React from 'react';
import PropTypes from 'prop-types';

class SortRenderer extends React.Component {
    static propTypes = {
        onUpdate: PropTypes.func.isRequired
    }

    constructor(props) {
        super(props);
        this.onChange = this.onChange.bind(this);
    }

    componentDidMount() {
        this.sort.focus();
    }
    componentWillUnmount() {
        this.sort.blur();
    }
    getValue() {
        return parseInt(this.sort.value, 10);
    }

    onChange() {
        this.label.innerText = this.sort.value == 16777215 ? '-' : this.sort.value;
    }

    render() {
        const { value, onUpdate, className, ...rest } = this.props;
        return (
            <>
                <input onChange={this.onChange} className={className + " form-control editor edit-text"} key="sort" ref={(node) => { this.sort = node; }} type="range" min="1" max="16777215" {...rest} />
                <label key="label" ref={(node) => this.label = node}>{value == 16777215 ? '-': value}</label>
            </>
        );
    }
}

export default SortRenderer;