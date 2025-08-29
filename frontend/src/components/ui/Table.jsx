

const Table = ({ children, className = '' }) => {
    return (
        <div className="overflow-x-auto">
            <table className={`min-w-full divide-y divide-gray-200 ${className}`}>
                {children}
            </table>
        </div>
    );
};

const TableHeader = ({ children, className = '' }) => (
    <thead className={`bg-gray-50 ${className}`}>
        {children}
    </thead>
);

const TableBody = ({ children, className = '' }) => (
    <tbody className={`bg-white divide-y divide-gray-200 ${className}`}>
        {children}
    </tbody>
);

const TableRow = ({ children, className = '', onClick, hover = false }) => {
    const hoverClass = hover ? 'hover:bg-gray-50' : '';
    const clickableClass = onClick ? 'cursor-pointer' : '';
    
    return (
        <tr 
            className={`${hoverClass} ${clickableClass} ${className}`}
            onClick={onClick}
        >
            {children}
        </tr>
    );
};

const TableHead = ({ children, className = '', sortable = false, onSort }) => {
    const sortableClass = sortable ? 'cursor-pointer hover:bg-gray-100' : '';
    
    return (
        <th 
            className={`px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ${sortableClass} ${className}`}
            onClick={sortable ? onSort : undefined}
        >
            <div className="flex items-center space-x-1">
                <span>{children}</span>
                {sortable && (
                    <i className="fas fa-sort text-gray-400"></i>
                )}
            </div>
        </th>
    );
};

const TableCell = ({ children, className = '' }) => (
    <td className={`px-6 py-4 whitespace-nowrap ${className}`}>
        {children}
    </td>
);

Table.Header = TableHeader;
Table.Body = TableBody;
Table.Row = TableRow;
Table.Head = TableHead;
Table.Cell = TableCell;

export default Table;
