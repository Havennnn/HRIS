/**
 * Dummy Data for Careers and Applications
 *
 * This file exports sample data for development and testing purposes.
 */

export interface CareerData {
    id: number;
    position: string;
    description: string;
    is_active: boolean;
}

export interface ApplicationData {
    id: number;
    career: string;
    full_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status: {
        label: string;
        variant: string;
    }[];
    interview?: ApplicationInterviewData | null;
}

export interface ApplicationInterviewData {
    id: number;
    application_id: number;
    scheduled_at: string;
    location: string;
    interviewer_name: string;
    notes: string;
    score: number | null;
    feedback: string;
    result: 'pending' | 'passed' | 'failed';
}

/**
 * HR Admin data for interview assignees
 */
export interface HrAdminData {
    id: number;
    name: string;
    email: string;
    position: string;
}

/**
 * Sample Careers data
 * Based on Career model: position, description, is_active
 */
export const careers: CareerData[] = [
    {
        id: 1,
        position: 'Backend Developer',
        description: 'We are looking for a talented Software Engineer to join our team. You will be responsible for developing and maintaining our warehouse management system.',
        is_active: true,
    },
    {
        id: 2,
        position: 'Project Manager',
        description: 'Seeking an experienced Project Manager to oversee multiple warehouse operations projects and ensure timely delivery.',
        is_active: true,
    },
    {
        id: 3,
        position: 'Warehouse Supervisor',
        description: 'Join our logistics team as a Warehouse Supervisor. Manage daily operations and ensure efficiency in inventory handling.',
        is_active: true,
    },
];

/**
 * Sample Applications data
 * Based on Application model: career, full_name, birthdate, mobile_number, email, status
 *
 * Status values:
 * { label: 'Pending', variant: 'badge-pending' } = PENDING
 * { label: 'Reviewing', variant: 'badge-reviewing' } = REVIEWING
 * { label: 'Interview', variant: 'badge-interview' } = INTERVIEW
 * { label: 'Rejected', variant: 'badge-rejected' } = REJECTED
 * { label: 'Hired', variant: 'badge-hired' } = HIRED
 */
export const applications: ApplicationData[] = [
    {
        id: 1,
        career: 'Backend Developer',
        full_name: 'John Smith',
        birthdate: '1990-05-15',
        mobile_number: '+63 912 345 6789',
        email: 'john.smith@email.com',
        status: [
            { label: 'Pending', variant: 'badge-pending' },
        ],
    },
    {
        id: 2,
        career: 'Project Manager',
        full_name: 'Maria Garcia',
        birthdate: '1988-08-22',
        mobile_number: '+63 917 234 5678',
        email: 'maria.garcia@email.com',
        status: [
            { label: 'Reviewing', variant: 'badge-reviewing' },
        ],
    },
    {
        id: 3,
        career: 'Warehouse Supervisor',
        full_name: 'Robert Chen',
        birthdate: '1985-03-10',
        mobile_number: '+63 918 345 6789',
        email: 'robert.chen@email.com',
        status: [
            { label: 'Interview', variant: 'badge-interview' },
        ],
        interview: {
            id: 1,
            application_id: 3,
            scheduled_at: '2026-03-15 10:00:00',
            location: 'Head Office - Conference Room A',
            interviewer_name: 'Jane Doe',
            notes: 'Technical interview for backend development skills',
            score: null,
            feedback: '',
            result: 'pending',
        },
    },
];

/**
 * Get a career by ID
 */
export const getCareerById = (id: number): CareerData | undefined => {
    return careers.find(career => career.id === id);
};

/**
 * Get active careers only
 */
export const getActiveCareers = (): CareerData[] => {
    return careers.filter(career => career.is_active);
};

/**
 * Get an application by ID
 */
export const getApplicationById = (id: number): ApplicationData | undefined => {
    return applications.find(application => application.id === id);
};

/**
 * Get interview by application ID
 */
export const getInterviewByApplicationId = (applicationId: number): ApplicationInterviewData | undefined => {
    const app = applications.find(application => application.id === applicationId);
    return app?.interview;
};

/**
 * Get applications by job/career ID
 */
export const getApplicationsByJobId = (jobId: number): ApplicationData[] => {
    return applications.filter(application => application.id === jobId);
};

/**
 * Get applications by status
 */
export const getApplicationsByStatus = (statusLabel: string): ApplicationData[] => {
    return applications.filter(application =>
        application.status.some(s => s.label === statusLabel)
    );
};

/**
 * Status labels mapping
 */
export const applicationStatusLabels: Record<number, string> = {
    1: 'Pending',
    2: 'Reviewing',
    3: 'Interview',
    4: 'Rejected',
    5: 'Hired',
};

/**
 * Get status label by status value
 */
export const getStatusLabel = (status: number): string => {
    return applicationStatusLabels[status] || 'Unknown';
};

/**
 * Get positions for selector (Option format)
 */
export const getPositionOptions = (): { value: string; label: string }[] => {
    return careers.map(career => ({
        value: career.id.toString(),
        label: career.position,
    }));
};

/**
 * Get active positions for selector
 */
export const getActivePositionOptions = (): { value: string; label: string }[] => {
    return getActiveCareers().map(career => ({
        value: career.id.toString(),
        label: career.position,
    }));
};

/**
 * Get status options for selector
 */
export const getStatusOptions = (): { value: string; label: string }[] => {
    return Object.entries(applicationStatusLabels).map(([value, label]) => ({
        value,
        label,
    }));
};

/**
 * HR Admin / Interviewer data
 */
export const hrAdmins: HrAdminData[] = [
    {
        id: 1,
        name: 'Jane Doe',
        email: 'jane.doe@company.com',
        position: 'HR Manager',
    },
    {
        id: 2,
        name: 'John Smith',
        email: 'john.smith@company.com',
        position: 'HR Specialist',
    },
    {
        id: 3,
        name: 'Sarah Wilson',
        email: 'sarah.wilson@company.com',
        position: 'Recruitment Officer',
    },
    {
        id: 4,
        name: 'Michael Brown',
        email: 'michael.brown@company.com',
        position: 'HR Coordinator',
    },
];

/**
 * Get HR admin options for selector
 */
export const getHrAdminOptions = (): { value: string; label: string }[] => {
    return hrAdmins.map(admin => ({
        value: admin.id.toString(),
        label: `${admin.name} - ${admin.position}`,
    }));
};
