const DEFAULT = 'af/get-data'
const SESSION_DATA = 'af/api/v1/session-data'
const MORE_INFO = 'af/more-info'

const client = flag => {
  let path = ''
  switch (flag) {
    case 'session_data':
      path = SESSION_DATA
      break
    case 'more_info':
      path = MORE_INFO
      break
    default:
      path = DEFAULT
  }

  const baseURL = window.drupalSettings.path.baseUrl + path

  return {
    request({ params = {} } = {}) {
      const query = new URLSearchParams(params).toString()
      const url = query ? `${baseURL}?${query}` : baseURL
      return fetch(url)
        .then(res => res.json())
        .then(data => ({ data }))
    }
  }
}

export default client
