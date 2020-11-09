const sortFormatter = (cell, row) => {
    if(row.sort == 16777215) {
        return '-';
    }else {
        return row.sort;
    }
}

export default sortFormatter;