import Input from '../ui/Input';
import Button from '../ui/Button';

const ParkingFilters = ({
    filters,
    onChange,
    onReset
}) => {
    return (
        <div className="glass-card p-6 mb-8">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-semibold">Filters</h2>
                <Button variant="outline" size="small" onClick={onReset} icon="fas fa-rotate-left">
                    Reset
                </Button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Input
                    label="Location"
                    placeholder="Search by location..."
                    value={filters.location}
                    onChange={(e) => onChange('location', e.target.value)}
                />

                <div>
                    <label className="block text-sm font-medium mb-2">Status</label>
                    <select
                        className="input"
                        value={filters.status}
                        onChange={(e) => onChange('status', e.target.value)}
                    >
                        <option value="">All</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>

                <Input
                    label="Max Rate ($/hour)"
                    type="number"
                    placeholder="Max hourly rate"
                    value={filters.maxRate}
                    onChange={(e) => onChange('maxRate', e.target.value)}
                />
            </div>
        </div>
    );
};

export default ParkingFilters;


