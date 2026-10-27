import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import localizedFormat from 'dayjs/plugin/localizedFormat'

dayjs.extend(relativeTime)
dayjs.extend(localizedFormat)

export const formatDate = (date, format = 'DD MMM YYYY') => {
    if (!date) return '—'
    return dayjs(date).format(format)
}

export const formatDateTime = (date, format = 'DD MMM YYYY, hh:mm A') => {
    if (!date) return '—'
    return dayjs(date).format(format)
}

export const fromNow = (date) => {
    if (!date) return '—'
    return dayjs(date).fromNow()
}

export default dayjs
