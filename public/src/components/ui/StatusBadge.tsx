import { ProspectStatus } from '../../types';

const config: Record<ProspectStatus, { label: string; className: string }> = {
  new: { label: 'New', className: 'bg-slate-100 text-slate-600' },
  qualified: { label: 'Qualified', className: 'bg-blue-50 text-blue-700' },
  contacted: { label: 'Contacted', className: 'bg-amber-50 text-amber-700' },
  'follow-up': { label: 'Follow-up', className: 'bg-orange-50 text-orange-700' },
  meeting: { label: 'Meeting', className: 'bg-violet-50 text-violet-700' },
  client: { label: 'Client', className: 'bg-emerald-50 text-emerald-700' },
};

export default function StatusBadge({ status }: { status: ProspectStatus }) {
  const { label, className } = config[status] ?? config.new;
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${className}`}>
      {label}
    </span>
  );
}
